$(document).ready(function () {
    if ($('input[name="year"]').val() < 1000) {
        $("#classyear-row").hide();
        $('input[name="year"]').prop("disabled", true);
    }
    $("#phone_number").mask("999-999-9999", { placeholder: " " });
    $('input[name="status"]').on("change", toggleClassYearRow);
});

function toggleClassYearRow() {
    console.log($(this));
    console.log($(this).val());
    if ($(this).val() == "student") {
        $("#classyear-row").show();
        $('input[name="year"]').prop("disabled", false);
    } else {
        $("#classyear-row").hide();
        if ($(this).val() == "faculty") {
            $('input[name="year"]').val(1);
        } else if ($(this).val() == "staff") {
            $('input[name="year"]').val(2);
        }
    }
}
