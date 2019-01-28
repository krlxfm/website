$(document).ready(function() {
    $('input[name="remote"]').on('change', toggleVideoConferencing);
    $('button[data-action="set-all-3"]').on('click', setAllSlotsAvailable);
    $('td.table-success, td.table-warning, td.table-danger').on('click', clickInternalInput);
    toggleVideoConferencing();
});

function setAllSlotsAvailable() {
    $('input[data-readable-value="available"]').prop('checked', true);
}

function clickInternalInput() {
    $(this).find('input').prop("checked", true);
}

function toggleVideoConferencing() {
    const abroadNow = parseInt($('input[name="remote"]:checked').val());
    $("#video-fields").toggle(abroadNow == 1);
}
