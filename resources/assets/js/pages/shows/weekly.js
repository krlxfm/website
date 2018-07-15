function removePreference(index) {
    removeSlot(preferences, "preference", index);
}

function removeConflict(index) {
    removeSlot(conflicts, "conflict", index);
}

function editConflict(index) {
    var conflict = conflicts[index];
    $('[name="conflict-days"]').prop('checked', false);
    conflict.days.forEach((day) => {
        $('#conflict-days-'+day).prop('checked', true);
    });
    $("#conflict-start").val(conflict.start);
    setConflictEndTime();
    $("#conflict-end").val(conflict.end);
    $("#conflict-index").val(index);
    $("#conflict-manager").modal('show');
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

function setConflictEndTime() {
    var start = moment($("#conflict-start").val(), 'HH:mm');
    var time = moment(start);
    $("#conflict-end").empty();
    for(var i = 0; i < 48; i++) {
        time.add(30, 'm');
        $("#conflict-end").append('<option value="'+time.format('HH:mm')+'">'+time.format('h:mm a')+(time.day() == start.day() ? '' : ' (next day)')+'</option>');
    }
}

function showNewConflictModal() {
    $('[name="conflict-days"]').prop('checked', false);
    $("#conflict-start").val("12:00");
    setConflictEndTime();
    $("#conflict-index").val(-1);
    $("#conflict-end").val("12:30");
    $("#conflict-manager").modal('show');
}

function saveConflict() {
    var conflict = {
        start: $("#conflict-start").val(),
        end: $("#conflict-end").val(),
        days: $.makeArray($('[name="conflict-days"]:checked').map((index, element) => { return $(element).val() }))
    }
    var index = $("#conflict-index").val();
    if(index == -1) {
        conflicts.push(conflict);
    } else {
        conflicts.splice(index, 1, conflict);
    }
    $("#conflict-manager").modal('hide');
}

$(document).ready(function() {
    $("#conflict-start").change(setConflictEndTime);
    $("#add-conflict-button").click(showNewConflictModal);
});
