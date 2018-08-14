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

function createEventSource(id, zone) {
    return {
        id: id,
        rendering: 'background',
        color: priorityColors[zone],
        events: []
    }
}

exports.priorityColors = priorityColors;

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
