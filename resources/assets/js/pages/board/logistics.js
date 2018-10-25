$(document).ready(function() {
    $('input[name="remote"]').on('change', toggleVideoConferencing);
    $('td.table-success, td.table-warning, td.table-danger').on('click', clickInternalInput);
    toggleVideoConferencing();
});

function clickInternalInput() {
    $(this).find('input').prop("checked", true);
}

function toggleVideoConferencing() {
    const abroadNow = parseInt($('input[name="remote"]:checked').val());
    $("#video-fields").toggle(abroadNow == 1);
}
