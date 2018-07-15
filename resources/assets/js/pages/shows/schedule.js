function removePreference(index) {
    removeSlot(preferences, "preference", index);
}

function removeConflict(index) {
    removeSlot(conflicts, "conflict", index);
}

function removeSlot(group, singular, index) {
    initialWarning(group, singular, index)
    .then((choice) => {
        if(!choice) throw new Error();
    })
    .then(() => {
        group.splice(index, 1);
    })
    .catch((err) => {});
}

function initialWarning(group, singular, index) {
    return swal({
        title: "Remove this "+singular+"?",
        text: conflictOrPrefToText(group[index]),
        icon: "warning",
        buttons: true,
        dangerMode: true
    })
}

function conflictOrPrefToText(item) {
    return item.days.join(", ") + ' ' + to12Hour(item.start) + ' - ' + to12Hour(item.end);
}
