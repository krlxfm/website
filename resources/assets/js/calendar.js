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

exports.transformConflicts = function(set) {
    return transformScheduleIntoEvents(set, 'j');
}

function transformScheduleIntoEvents(set, zone) {
    var eventList = [];
    set.forEach(item => {
        eventList.push({
            color: priorityColors[zone],
            start: item.start,
            end: endTime(item.start, item.end),
            dow: item.days.map(day => weekdayMapping.indexOf(day))
        });
    });
};

exports.parseTime = parseTime;
exports.priorityColors = priorityColors;
exports.weekdayMapping = weekdayMapping;
