const unique = require('array-unique');
const flatten = require('flatten');

const priorityColors = {
    "a": '#1b1c1d',
    "b": '#767676',
    "c": '#6435c9',
    "d": '#2185d0',
    "e": '#00b5ad',
    "f": '#21ba45',
    "g": '#b5cc18',
    "h": '#fbbd08',
    "i": '#f2711c',
    "j": '#db2828',
    "s": '#e8e8e8'
};

function weeklyGrid() {
    var grid = [];
    for(var i = 0; i < 7; i++) {
        var dayGrid = {};
        for(var h = 0; h < 24; h++) {
            dayGrid[h.toString().padStart(2, '0')+':00'] = null;
            dayGrid[h.toString().padStart(2, '0')+':30'] = null;
        }
        grid.push(dayGrid);
    }
    return grid;
}

const weekdayMapping = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

function endTime(startTime, endTime) {
    const rightNow = moment();
    const parsedEnd = parseTime(endTime);
    const start = moment(rightNow).set(parseTime(startTime));
    const end = moment(rightNow).set(parsedEnd);
    if(end.isSameOrBefore(start)) {
        return (parsedEnd.hour + 24) + ':' + endTime.split(':')[1];
    } else {
        return endTime;
    }
}

function parseTime(time) {
    const components = time.split(':');
    return {
        'hour': parseInt(components[0]),
        'minute': parseInt(components[1])
    }
}

function throwSchedulingFault(message, type, fixFunction = null) {
    app.controlMessages[type + 's'].push({
        message,
        fixable: (fixFunction != null),
        fixFunction
    });
}

function checkNoOverlaps(shows) {
    var grid = weeklyGrid();
    shows.forEach(show => {
        var time = moment(show.start);
        while(time.isBefore(show.end)) {
            if(grid[time.day()][time.format('HH:mm')]) {
                throwSchedulingFault("Two shows are scheduled simultaneously at "+time.format('dddd h:mm a'), 'error');
            } else {
                grid[time.day()][time.format('HH:mm')] = show.id;
            }
            time.add(30, 'm');
        }
    })
    return grid;
}

function checkNoConflictOverlaps(shows, field) {
    shows.forEach(show => {
        var conflicts;
        if(field == 'classes') {
            conflicts = exports.transformClasses(showList[show.id].classes);
        } else {
            conflicts = exports.transformConflicts(showList[show.id].conflicts);
        }
        var fault = checkConflictSetAgainstShow(conflicts, show);
        if(fault) {
            throwSchedulingFault("A show is scheduled during a declared " + (field == 'classes' ? 'class' : 'conflict') + " at " + fault.format('dddd h:mm a'), (field == 'classes' ? 'error' : 'warning'));
        }
    });
}

function checkConflictSetAgainstShow(conflicts, show) {
    var fault = null;
    conflicts.forEach(conflict => {
        if(fault) return true;
        conflict.dow.forEach(day => {
            if(fault) return true;
            var time = moment().day(day).set(parseTime(conflict.start)).seconds(0);
            var endTime = moment(time).set(parseTime(conflict.end)).seconds(0);
            while(time.isBefore(endTime)) {
                if(time.isSameOrAfter(show.start.format('YYYY-MM-DD HH:mm')) && time.isBefore(show.end.format('YYYY-MM-DD HH:mm'))) {
                    fault = time;
                    break;
                }
                time.add(30, 'm');
            }
        })
    })
    return fault;
}

function checkNoLongShows(shows) {
    shows.forEach(show => {
        var diff = show.end.diff(show.start);
        if(diff > moment.duration(2, 'hours').asMilliseconds()) {
            throwSchedulingFault("The show starting at " + show.start.format('dddd h:mm a') + " is over two hours long", 'warning');
        }
    })
}

function checkNoWeekendTransition(grid) {
    if(grid[0]["00:00"] && grid[6]["23:30"] && grid[0]["00:00"] == grid[6]["23:30"]) {
        throwSchedulingFault("Shows should change at midnight between Saturday and Sunday", 'warning');
    }
}

function checkNoSpringForwardShenanigans(grid) {
    if(!moment(term.on_air).isDST() && moment(term.off_air).isDST()) {
        if(grid[0]["02:00"] != grid[0]["02:30"]) {
            throwSchedulingFault("Due to Spring Forward, don't schedule a change at 2:30 am Sunday", 'warning');
        }
    }
}

function check1aMercyRule(shows) {
    const showsWithEarlyClasses = shows.filter(show => showList[show.id].classes.filter(i => earlyClasses.includes(i)).length > 0);
    const ecShowsWithEarlyStart = showsWithEarlyClasses.filter(show => (show.start.hour() >= 1 && show.end.hour() < 8 && show.end.hour() > 1));

    /*
     * We now have a list of shows that could be subjected to the 1a Mercy Rule.
     * For each of these shows, check if their starting day of the week matches
     * with their early classes. If these match, flag the show.
     */
    ecShowsWithEarlyStart.forEach(show => {
        const myEarlyClasses = showList[show.id].classes.filter(i => earlyClasses.includes(i));
        const myEarlyDays = myEarlyClasses.map(i => classTimes[i].times.map(t => t.days));
        if(unique(flatten(myEarlyDays)).includes(show.start.format('dddd'))) {
            throwSchedulingFault("Due to a same-day early morning class, try to move the " + show.start.format('dddd h:mm a') + " show to a different day or time.", 'warning');
        }
    });
}

function computeDiffs() {
    var diffs = {};
    shows.forEach(show => {
        if(show.start == show.published_start && show.end == show.published_end && show.day == show.published_day) {
            return true;
        } else if (!show.start || !show.end || !show.day) {
            diffs[show.id] = "rm";
        } else if (!show.published_start || !show.published_end || !show.published_day) {
            diffs[show.id] = "new";
        } else {
            diffs[show.id] = "mv";
        }
    });
    app.diffs = diffs;
}

exports.checkForErrors = function () {
    app.controlMessages = {errors: [], warnings: [], suggestions: []};
    var allShows = $("#calendar").fullCalendar('clientEvents', calEvent => calEvent.id != null);
    var recurringShows = $("#calendar").fullCalendar('clientEvents', calEvent => (calEvent.id != null && !calEvent.id.includes('-')));

    // Errors - these are "show-stoppers" and block publishing.
    var grid = checkNoOverlaps(allShows);
    checkNoConflictOverlaps(recurringShows, 'classes');

    // Warnings - these allow a schedule to publish, but warn you of potentially bad ideas.
    checkNoConflictOverlaps(recurringShows, 'conflicts');
    checkNoLongShows(recurringShows);
    checkNoWeekendTransition(grid);
    checkNoSpringForwardShenanigans(grid);
    check1aMercyRule(recurringShows);

    computeDiffs();
};

exports.transformClasses = function (set) {
    var eventList = [];
    set.map(item => window.classTimes[item].times).forEach(classTime => {
        eventList = eventList.concat(transformScheduleIntoEvents(classTime, 'a'));
    });
    return eventList;
};

exports.transformConflicts = function (set) {
    return transformScheduleIntoEvents(set, 'j');
}

exports.transformPreferences = function (set) {
    var events = [];
    const prefStrengthMap = ['j', 'h', 'f', 'd'];
    prefStrengthMap.forEach((zone, index) => {
        events = events.concat(transformScheduleIntoEvents(set.filter(item => item.strength == index), zone));
    });
    return events;
}

function transformScheduleIntoEvents(set, zone) {
    var eventList = [];
    set.forEach(item => {
        const newEvent = {
            color: priorityColors[zone],
            start: item.start,
            end: endTime(item.start, item.end),
            dow: item.days.map(day => weekdayMapping.indexOf(day))
        };
        eventList.push(newEvent);
        if(parseInt(newEvent.end.split(':')[0]) >= 24 && newEvent.dow.includes(6)) {
            eventList.push({
                color: priorityColors[zone],
                start: '00:00',
                end: item.end,
                dow: [0]
            });
        }
    });
    return eventList;
};

exports.parseTime = parseTime;
exports.priorityColors = priorityColors;
exports.weekdayMapping = weekdayMapping;
