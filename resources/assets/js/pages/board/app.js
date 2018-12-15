$(document).ready(function() {
    $('button[data-action="delete-position"]').click(deletePosition);
});

function deletePosition() {
    var button = $(this);
    swal({
        title: "Remove "+button.data('position')+"?",
        text: "Are you sure you want to remove your responses to the "+button.data('position')+" position? You can add the position back later, but you will need to re-enter your responses.",
        icon: "warning",
        buttons: true,
        dangerMode: true
    })
    .then((response) => {
        if (response) {
            $('form[data-position-id="'+button.data('posid')+'"]').submit();
        }
    })
}
