require('./bootstrap');

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
        droppable: true
    });
}

window.enableDragging = function() {
    $('.schedule-queue-item').each(function() {
        const showID = $(this).data('showId');
        // store data so the calendar knows to render an event upon drop
        $(this).data('event', {
            id: showID,
            title: showList[showID].title,
            stick: true
        });

        // make the event draggable using jQuery UI
        $(this).draggable({
            zIndex: 999,
            revert: true,      // will cause the event to go back to its
            revertDuration: 0  //  original position after the drag
        });
    });
}

const schedule = new Vue({
    el: '#fluid-sector'
});
