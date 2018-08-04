$(document).ready(function() {
    $("#changes-saved-item").hide();
    $("#scheduling-form input, #scheduling-form textarea, #scheduling-form select").change(submitField);
    $("#scheduling-form input, #scheduling-form textarea").keyup(submitAfterTimeout);
})
