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
            dayGrid[h.toString().padStart(2, '0')+':00'] = '';
            dayGrid[h.toString().padStart(2, '0')+':30'] = '';
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
            if(grid[time.day()][time.format('HH:mm')].length != 0) {
                throwSchedulingFault("Two shows are scheduled simultaneously at "+time.format('dddd h:mm a'), 'error');
            } else {
                grid[time.day()][time.format('HH:mm')] = show.id;
            }
            time.add(30, 'm');
        }
    })
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
            throwSchedulingFault("A show is scheduled during a declared " + (field == 'classes' ? 'class' : 'conflict') + " at " + fault.format('dddd hh:mm a'), (field == 'classes' ? 'error' : 'warning'));
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
                if(time.isSameOrAfter(show.start.format('YYYY-MM-DD hh:mm')) && time.isBefore(show.end.format('YYYY-MM-DD hh:mm'))) {
                    fault = time;
                    break;
                }
                time.add(30, 'm');
            }
        })
    })
    return fault;
}

exports.checkForErrors = function () {
    app.controlMessages = {errors: [], warnings: [], suggestions: []};
    var calendarShows = $("#calendar").fullCalendar('clientEvents', calEvent => calEvent.id != null);

    // Errors - these are "show-stoppers" and block publishing.
    checkNoOverlaps(calendarShows);
    checkNoConflictOverlaps(calendarShows, 'classes');

    // Warnings - these allow a schedule to publish, but warn you of potentially bad ideas.
    checkNoConflictOverlaps(calendarShows, 'conflicts');
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
