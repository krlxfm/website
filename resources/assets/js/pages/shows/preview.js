$(document).ready(updateSchedulePreview);

const nextDay = {
    "Sunday": "Monday",
    "Monday": "Tuesday",
    "Tuesday": "Wednesday",
    "Wednesday": "Thursday",
    "Thursday": "Friday",
    "Friday": "Saturday",
    "Saturday": "Sunday"
};

function updateSchedulePreview() {
    var sortedPreferences = preferences.slice(0).sort((a, b) => {
        return a.strength - b.strength;
    });
    $("#schedule-preview-main rect").attr('class', '');
    setSchedulePreviewItems(sortedPreferences, ['s', 'h', 'f', 'd']);
    setSchedulePreviewItems(conflicts, 'j');
    var classList = [];
    var hasCheckboxes = $('[name="classes"]');
    $('[name="classes"]:checked').each((index, checkbox) => {
        classList = classList.concat(classTimes[$(checkbox).val()].times);
    });
    if(hasCheckboxes.length == 0) {
        classes.forEach((classID) => {
            classList = classList.concat(classTimes[classID].times);
        });
    }
    setSchedulePreviewItems(classList, 'a');
}

function setSchedulePreviewItems(items, zones) {
    items.forEach((item) => {
        var start = moment(item.start, 'HH:mm');
        var end = moment(item.end, 'HH:mm');
        if (end <= start) end.add(1, 'd');
        item.days.forEach((day) => {
            var time = moment(start);
            while(time < end) {
                var dayToUpdate = ((time.hours() > start.hours() || time.hours() == start.hours() && time.hours() >= start.hours()) ? day : nextDay[day]);
                var rect = $("#preview-rect-"+dayToUpdate + '-' + time.format('HH-mm'));
                if(Array.isArray(zones)) {
                    rect.attr('class', 'svg-bg-priority-' + zones[item.strength]);
                } else {
                    rect.attr('class', 'svg-bg-priority-' + zones);
                }
                time.add(30, 'm');
            }
        })
    })
}
