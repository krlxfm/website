$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('td.table-success, td.table-warning, td.table-danger').on('click', clickInternalInput);
    $("#save-with-email").click(saveAndNotifyCandidates);
});

function saveAndNotifyCandidates() {
    $("#notify-field").val("1");
    $("#interviews-form").submit();
}

function clickInternalInput() {
    $(this).find('input').prop("checked", true);
}
