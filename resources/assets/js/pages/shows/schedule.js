$(document).ready(function() {
    $("#changes-saved-item").hide();
    setStandardListeners('#scheduling-form input, #scheduling-form textarea');
    $("#scheduling-form select").change(submitField);
})
