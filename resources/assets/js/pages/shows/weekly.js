function removePreference(index) {
    removeSlot(preferences, "preference", index);
}

function removeConflict(index) {
    removeSlot(conflicts, "conflict", index);
}

function editConflict(index) {
    var conflict = conflicts[index];
    editItem(conflict, 'conflict');
}

function editPreference(index) {
    var preference = preferences[index];
    $("#preference-strength").val(preference.strength);
    editItem(preference, 'preference');
}

function editItem(item, type) {
    $('[name="'+type+'-days"]').prop('checked', false);
    item.days.forEach((day) => {
        $('#'+type+'-days-'+day).prop('checked', true);
    });
    $("#"+type+"-start").val(item.start);
    setEndTime(type);
    $("#"+type+"-end").val(item.end);
    $("#"+type+"-index").val(index);
    $("#"+type+"-manager").modal('show');
}

function setEndTime(type) {
    if(type == 'conflict') {
        setConflictEndTime();
    } else {
        setPreferenceEndTime();
    }
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
    populateEndDropdown('conflict');
}

function setPreferenceEndTime() {
    populateEndDropdown('preference');
    var slotLength = parseInt($('[name="preferred_length"]:checked').val());
    var start = moment($("#preference-start").val(), 'HH:mm');
    time = moment(start).add(slotLength, 'm');
    $("#preference-end").val(time.format('HH:mm'));
}

function populateEndDropdown(menu) {
    var start = moment($("#"+menu+"-start").val(), 'HH:mm');
    var time = moment(start);
    $("#"+menu+"-end").empty();
    for(var i = 0; i < 48; i++) {
        time.add(30, 'm');
        $("#"+menu+"-end").append('<option value="'+time.format('HH:mm')+'">'+time.format('h:mm a')+(time.day() == start.day() ? '' : ' (next day)')+'</option>');
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
    $("#preference-manager").modal('show');
}

function saveConflict() {
    storeScheduleItem('conflict', conflicts);
}

function storeScheduleItem(group, list) {
    var item = {
        start: $("#"+group+"-start").val(),
        end: $("#"+group+"-end").val(),
        days: $.makeArray($('[name="'+group+'-days"]:checked').map((index, element) => { return $(element).val() }))
    };
    if (group == 'preference') item.strength = $("#"+group+"-strength").val();

    $("#"+group+"-manager").modal('hide');
    if(item.days.length == 0) {
        return true;
    }

    var index = $("#"+group+"-index").val();
    if(index == -1) {
        list.push(item);
    } else {
        list.splice(index, 1, item);
    }
    var apiData = {};
    apiData[group+'s'] = list;

    axios.patch('/api/v1/shows/'+showID, apiData)
    .then((response) => {
        $("#changes-saved-item").show();
        $("#changes-saved-item").fadeOut(2000);
    });
}

function savePreference() {
    storeScheduleItem('preference', preferences);
}

$(document).ready(function() {
    $("#conflict-start").change(setConflictEndTime);
    $("#preference-start").change(setPreferenceEndTime);
    $('button[data-toggle="add-conflict"]').click(showNewConflictModal);
    $('button[data-toggle="add-pref"]').click(showNewPreferenceModal);
});
