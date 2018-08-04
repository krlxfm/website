function setSchedulePreviewItems(items, zones) {
    items.forEach((item) => {
        var start = moment(item.start, 'HH:mm');
        var end = moment(item.end, 'HH:mm');
        if (end <= start) end.add(1, 'd');
        item.days.forEach((day) => {
            var time = moment(start);
            while(time < end) {
                var rect = $("#preview-rect-"+day + '-' + time.format('HH-mm'));
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
