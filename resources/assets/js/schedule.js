require('./bootstrap');
const calendar = require('./calendar');
const publish = require('./publish');

window.Vue = require('vue');

Vue.component('schedule-control-panel', require('./components/schedule/ControlPanel.vue'));
Vue.component('schedule-builder', require('./components/schedule/Builder.vue'));
Vue.component('schedule-queue', require('./components/schedule/Queue.vue'));
Vue.component('schedule-publisher', require('./components/schedule/Publisher.vue'));
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
        eventResize: modifyEvent,
        eventClick: selectAndDisplayEvent,
        eventDragStart: selectEvent,
        eventResizeStart: selectEvent,
    });
    calendar.checkForErrors();
}

function getEvents() {
    var showList = [];
    window.shows.forEach(show => {
        if(!show.day || !show.start || !show.end) return true;
        const showTimes = parseStartAndEnd(show.day, show.start, show.end);
        var showData = {
            id: show.id,
            title: (show.board_boost ? '[BOARD] ' : '') + show.title,
            color: calendar.priorityColors[show.priority_code.charAt(0).toLowerCase()],
            textColor: ['g', 'h', 's'].includes(show.priority_code.charAt(0).toLowerCase()) ? 'black' : 'white',
            start: showTimes.start,
            end: showTimes.end
        };
        showList.push(showData);
    });
    return showList.concat(transformTracks());
}

function parseStartAndEnd(day, start, end) {
    var startMoment = moment().day(0).startOf('day');
    startMoment.add(calendar.weekdayMapping.indexOf(day), 'days');
    startMoment.set(calendar.parseTime(start));
    var endMoment = moment(startMoment);
    endMoment.set(calendar.parseTime(end));
    if(endMoment.isSameOrBefore(startMoment)) {
        endMoment.add(1, 'day');
    }

    return {start: startMoment, end: endMoment};
}

function transformTracks() {
    return tracks.map(track => {
        const trackTimes = parseStartAndEnd(track.start_day, track.start_time, track.end_time);
        return {
            id: 'HOLD-' + track.id,
            title: track.name,
            color: calendar.priorityColors['s'],
            textColor: 'black',
            start: trackTimes.start,
            end: trackTimes.end,
            editable: false
        }
    });
}

function selectEvent(calEvent) {
    app.showID = calEvent.id;
}

function selectAndDisplayEvent(calEvent) {
    selectEvent(calEvent);
    displaySchedule(calEvent.id);
}

function displaySchedule(showID) {
    $("#calendar").fullCalendar('removeEventSource', 'base');

    if(showID.includes('-')) return true;
    var source = {id: 'base', rendering: 'background', events: []};
    const show = showList[showID];
    source.events = source.events.concat(calendar.transformPreferences(show.preferences));
    source.events = source.events.concat(calendar.transformConflicts(show.conflicts));
    source.events = source.events.concat(calendar.transformClasses(show.classes));

    $("#calendar").fullCalendar('addEventSource', source);
}

function dropEvent(date) {
    var show = showList[$(this).data('event').id];
    setShowTime(show, date, moment(date).add(show.preferred_length, 'm'));
}

function modifyEvent(calEvent) {
    setShowTime(showList[calEvent.id], calEvent.start, calEvent.end);
}

function setShowTime(show, start, end) {
    show.day = start ? start.format('dddd') : null;
    show.start = start ? start.format('HH:mm') : null;
    show.end = end ? end.format('HH:mm') : null;
    axios.patch('/api/v1/schedule/' + show.id, {
        start: show.start,
        end: show.end,
        day: show.day
    });
    calendar.checkForErrors();
}

function syncAllShowsToAPI() {
    var showList = Object.values(window.showList).map(show => {
        return {
            id: show.id,
            date: null,
            day: show.day,
            start: show.start,
            end: show.end
        };
    });
    $("#syncing-modal").modal('show');
    axios.patch('/api/v1/schedule/sync', {shows: showList}).then(response => {
        $("#syncing-modal").modal('hide');
    });
}

window.vueData = {
    data: {
        currentItem: '',
        progress: 0,
        showID: '',
        diffs: {},
        controlMessages: {errors: [], warnings: [], suggestions: []}
    },
    methods: {
        checkPublishStatus: publish.checkPublishStatus,
        publish: publish.showModal,
        publishDraft: publish.publishDraft,
        publishFinal: publish.publishFinal,
        syncChanges: syncAllShowsToAPI,
        setCurrentShow: function(show) {
            this.showID = show;
            displaySchedule(show);
        },
        removeShow: function() {
            if(this.showID) {
                $("#calendar").fullCalendar('removeEvents', this.showID);
                setShowTime(window.showList[this.showID], null, null)
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
            color: calendar.priorityColors[show.priority_code.charAt(0).toLowerCase()],
            textColor: ['g', 'h', 's'].includes(show.priority_code.charAt(0).toLowerCase()) ? 'black' : 'white',
            duration: '0'+Math.floor(show.preferred_length / 60)+':'+((show.preferred_length % 60) / 10)+'0'
        });

        $(this).draggable({
            zIndex: 999,
            revert: true,
            revertDuration: 0
        });
    });
}
