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

function editPreference(index) {
    var preference = preferences[index];
    $('[name="preference-days"]').prop('checked', false);
    preference.days.forEach((day) => {
        $('#preference-days-'+day).prop('checked', true);
    });
    $("#preference-start").val(preference.start);
    setPreferenceEndTime();
    $("#preference-end").val(preference.end);
    $("#preference-index").val(index);
    $("#preference-manager").modal('show');
}

function removeSlot(group, singular, index) {
    initialWarning(group, singular, index)
    .then((choice) => {
        if(!choice) throw new Error();
    })
    .then(() => {
        group.splice(index, 1);
        return axios.patch('/api/v1/shows/'+showID, {
            conflicts: window.conflicts,
            preferences: window.preferences
        })
    })
    .then((response) => {
        $("#changes-saved-item").show();
        $("#changes-saved-item").fadeOut(2000);
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

function setPreferenceEndTime() {
    var start = moment($("#preference-start").val(), 'HH:mm');
    var time = moment(start);
    $("#preference-end").empty();
    for(var i = 0; i < 48; i++) {
        time.add(30, 'm');
        $("#preference-end").append('<option value="'+time.format('HH:mm')+'">'+time.format('h:mm a')+(time.day() == start.day() ? '' : ' (next day)')+'</option>');
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

function showNewPreferenceModal() {
    $('[name="preference-days"]').prop('checked', false);
    $("#preference-start").val("12:00");
    setPreferenceEndTime();
    $("#preference-index").val(-1);
    $("#preference-strength").val(1);
    $("#preference-end").val("12:30");
    $("#preference-manager").modal('show');
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
    var data = {
        conflicts: window.conflicts,
        preferences: window.preferences
    };
    console.log(JSON.parse(JSON.stringify(data)));
    axios.patch('/api/v1/shows/'+showID, {
        conflicts: window.conflicts,
        preferences: window.preferences
    })
    .then((response) => {
        $("#changes-saved-item").show();
        $("#changes-saved-item").fadeOut(2000);
    })
    .catch((err) => { console.log(err.response.data) })
    $("#conflict-manager").modal('hide');
}

function savePreference() {
    var preference = {
        strength: $("#preference-strength").val(),
        start: $("#conflict-start").val(),
        end: $("#conflict-end").val(),
        days: $.makeArray($('[name="conflict-days"]:checked').map((index, element) => { return $(element).val() }))
    }
    var index = $("#preference-index").val();
    if(index == -1) {
        preferences.push(preference);
    } else {
        preferences.splice(index, 1, preference);
    }
    axios.patch('/api/v1/shows/'+showID, {
        conflicts: window.conflicts,
        preferences: window.preferences
    })
    .then((response) => {
        $("#changes-saved-item").show();
        $("#changes-saved-item").fadeOut(2000);
    })
    $("#preference-manager").modal('hide');
}

$(document).ready(function() {
    $("#conflict-start").change(setConflictEndTime);
    $("#preference-start").change(setPreferenceEndTime);
    $("#add-conflict-button").click(showNewConflictModal);
    $("#add-preference-button").click(showNewPreferenceModal);
});