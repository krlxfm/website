$(document).ready(function() {
    $("#classyear-row").hide();
    $("#phone_number").mask('999-999-9999', {placeholder:" "});
    $('input[name="status"]').on('change', toggleClassYearRow);
});

function toggleClassYearRow() {
    console.log($(this));
    console.log($(this).val());
    $("#classyear-row").toggle($(this).val() == 'student');
}
