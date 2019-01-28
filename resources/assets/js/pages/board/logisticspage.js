$(document).ready(function() {
    $('input[name="remote"]').on('change', toggleVideoConferencing);
    $('button[data-action="set-all-3"]').on('click', setAllSlotsAvailable);
    $('td.table-success, td.table-warning, td.table-danger').on('click', clickInternalInput);
    toggleVideoConferencing();
});

function setAllSlotsAvailable() {
    if (confirm('Are you sure you want to set all interview times to "Available"? Note that this will override any "Unavailable" or "If need be" designations!')) {
        $('input[data-readable-value="available"]').prop('checked', true);
    }
}

function clickInternalInput() {
    $(this).find('input').prop("checked", true);
}

function toggleVideoConferencing() {
    const abroadNow = parseInt($('input[name="remote"]:checked').val());
    $("#video-fields").toggle(abroadNow == 1);
}
