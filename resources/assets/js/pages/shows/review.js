$(document).ready(function() {
    $("#changes-saved-item").hide();
})

function reviewAndSubmit() {
    swal({
        title: "Ready to submit?",
        text: "Ready to submit your show for scheduling? You can continue to edit your application after this if you need to.",
        buttons: true,
    })
    .then((choice) => {
        if(!choice) throw new Error();
    })
    .then(() => {
        axios.patch('/api/v1/shows/'+showID, {
            submitted: true
        })
        .then((response) => {
            window.location.reload();
        })
    })
    .catch((err) => {});
}
