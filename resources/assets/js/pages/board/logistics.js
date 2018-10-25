$(document).ready(function() {
    toggleVideoConferencing();
    $('input[name="remote"]').on('change', toggleVideoConferencing);
});

function toggleVideoConferencing() {
    const abroadNow = parseInt($('input[name="remote"]:checked').val());
    $("#video-fields").toggle(abroadNow);
}
