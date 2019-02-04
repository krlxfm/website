$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $("#save-with-email").click(saveAndNotifyCandidates);
});

function saveAndNotifyCandidates() {
    $("#notify-field").val("1");
    $("#interviews-form").submit();
}
