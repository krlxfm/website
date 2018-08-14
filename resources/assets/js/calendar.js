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
