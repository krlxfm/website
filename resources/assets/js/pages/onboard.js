$(document).ready(function() {
    $("#classyear-row").hide();
    $('input[name="status"]').on('change', toggleClassYearRow);
});

function toggleClassYearRow() {
    console.log($(this));
    console.log($(this).val());
    $("#classyear-row").toggle($(this).val() == 'student');
}
