function createEventSource(id, zone) {
    return {
        id: id,
        rendering: 'background',
        className: 'bg-priority-'+zone,
        events: []
    }
}

exports.baseEventSources = function () {
    const eventTypes = [
        {name: 'classes', zone: 'a'},
        {name: 'conflicts', zone: 'j'},
        {name: 'preferred', zone: 'h'},
        {name: 'strongly_preferred', zone: 'f'},
        {name: 'first_choice', zone: 'd'}
    ];
    return eventTypes.map(type => {
        return createEventSource(type.name, type.zone);
    })
}
