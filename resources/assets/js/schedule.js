require('./bootstrap');

window.Vue = require('vue');

Vue.component('schedule-builder', require('./components/schedule/Builder.vue'));
Vue.component('schedule-queue', require('./components/schedule/Queue.vue'));
Vue.component('schedule-inspector', require('./components/schedule/Inspector.vue'));

$(document).ready(function() {
    setupCalendar();
    enableDragging();
});

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

function setupCalendar() {
    $("#calendar").fullCalendar({
        header: false,
        defaultView: 'agendaWeek',
        columnHeaderFormat: 'ddd',
        allDaySlot: false,
        businessHours: {dow: [0, 1, 2, 3, 4, 5, 6], start: '06:00', end: '22:00'},
        now: moment().subtract(7, 'd'),
        defaultDate: moment(),
        height: function() { return window.innerHeight - 100; },
        editable: true,
        droppable: true,
        drop: dropEvent,
        events: getEvents(),
        eventClick: selectEvent,
        eventDragStart: selectEvent,
        eventResizeStart: selectEvent
    });

    console.log(getEvents());
}

function parseTime(time) {
    const components = time.split(':');
    return {
        'hour': parseInt(components[0]),
        'minute': parseInt(components[1])
    }
}

function getEvents() {
    var showList = [];
    const weekdayMapping = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    window.shows.forEach(show => {
        if(!show.day || !show.start || !show.end) return true;
        var showStart = moment().day(0).startOf('day');
        showStart.add(weekdayMapping.indexOf(show.day), 'days');
        showStart.set(parseTime(show.start));
        var showEnd = moment(showStart);
        showEnd.set(parseTime(show.end));
        if(showEnd.isSameOrBefore(showStart)) {
            showEnd.add(1, 'day');
        }
        var showData = {
            id: show.id,
            title: show.title,
            color: priorityColors[show.priority.charAt(0).toLowerCase()],
            textColor: ['g', 'h', 's'].includes(show.priority.charAt(0).toLowerCase()) ? 'black' : 'white',
            start: showStart,
            end: showEnd
        }
        showList.push(showData);
    });
    return showList;
}

function selectEvent(calEvent) {
    app.showID = calEvent.id
}

function dropEvent(date) {
    const showID = $(this).data('event').id;
    var show = showList[showID];
    show.day = date.format('dddd');
    show.start = date.format('HH:mm');
    show.end = moment(date).add(show.preferred_length, 'm').format('HH:mm');
}

window.vueData = {
    data: {
        showID: ''
    },
    methods: {
        setCurrentShow: function(show) {
            this.showID = show;
        }
    }
}

window.enableDragging = function() {
    $('.schedule-queue-item').each(function() {
        const showID = $(this).data('showId');
        const show = showList[showID];

        $(this).data('event', {
            id: showID,
            title: show.title,
            color: priorityColors[show.priority.charAt(0).toLowerCase()],
            textColor: ['g', 'h', 's'].includes(show.priority.charAt(0).toLowerCase()) ? 'black' : 'white',
            duration: '0'+Math.floor(show.preferred_length / 60)+':'+((show.preferred_length % 60) / 10)+'0'
        });

        $(this).draggable({
            zIndex: 999,
            revert: true,
            revertDuration: 0
        });
    });
}
