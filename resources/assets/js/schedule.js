require('./bootstrap');
const calendar = require('./calendar');

window.Vue = require('vue');

Vue.component('schedule-builder', require('./components/schedule/Builder.vue'));
Vue.component('schedule-queue', require('./components/schedule/Queue.vue'));
Vue.component('schedule-inspector', require('./components/schedule/Inspector.vue'));

$(document).ready(function() {
    setupCalendar();
    enableDragging();
});

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
        themeSystem: 'bootstrap4',
        drop: dropEvent,
        eventSources: [{ id: 'shows', events: getEvents() }],
        eventDrop: modifyEvent,
        eventClick: selectAndDisplayEvent,
        eventDragStart: selectEvent,
        eventResizeStart: selectEvent,
    });
}

function getEvents() {
    var showList = [];
    window.shows.forEach(show => {
        if(!show.day || !show.start || !show.end) return true;
        var showStart = moment().day(0).startOf('day');
        showStart.add(calendar.weekdayMapping.indexOf(show.day), 'days');
        showStart.set(calendar.parseTime(show.start));
        var showEnd = moment(showStart);
        showEnd.set(calendar.parseTime(show.end));
        if(showEnd.isSameOrBefore(showStart)) {
            showEnd.add(1, 'day');
        }
        var showData = {
            id: show.id,
            title: show.title,
            color: calendar.priorityColors[show.priority.charAt(0).toLowerCase()],
            textColor: ['g', 'h', 's'].includes(show.priority.charAt(0).toLowerCase()) ? 'black' : 'white',
            start: showStart,
            end: showEnd
        };
        showList.push(showData);
    });
    return showList;
}

function selectEvent(calEvent) {
    app.showID = calEvent.id;
    $("#calendar").fullCalendar('removeEventSource', 'base');
}

function selectAndDisplayEvent(calEvent) {
    selectEvent(calEvent);
    displaySchedule(calEvent.id);
}

function displaySchedule(showID) {
    var source = {id: 'base', rendering: 'background', events: []};
    const show = showList[showID];
    source.events = source.events.concat(calendar.transformScheduleIntoEvents(show.conflicts, 'j'));

    $("#calendar").fullCalendar('addEventSource', source);
}

function dropEvent(date) {
    const showID = $(this).data('event').id;
    var show = showList[showID];
    show.day = date.format('dddd');
    show.start = date.format('HH:mm');
    show.end = moment(date).add(show.preferred_length, 'm').format('HH:mm');
}

function modifyEvent(calEvent) {
    var show = showList[calEvent.id];
    show.day = calEvent.start.format('dddd');
    show.start = calEvent.start.format('HH:mm');
    show.end = calEvent.end.format('HH:mm');
}

window.vueData = {
    data: {
        showID: ''
    },
    methods: {
        setCurrentShow: function(show) {
            this.showID = show;
            displayShowSchedule(show);
        },
        removeShow: function() {
            if(this.showID) {
                $("#calendar").fullCalendar('removeEvents', this.showID);
                window.showList[this.showID].day = null;
                window.showList[this.showID].start = null;
                window.showList[this.showID].end = null;
                this.showID = '';
            }
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
            color: calendar.priorityColors[show.priority.charAt(0).toLowerCase()],
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
