$(document).ready(function() {
    $("#changes-saved-item").hide();
    $("#scheduling-form input, #scheduling-form textarea, #scheduling-form select").change(submitForm);
})
