$(document).ready(function() {
    $("#decline-invitation").click(declineInvitation);
})

function declineInvitation(e) {
    e.preventDefault();
    swal({
        title: "Decline this invitation?",
        text: "Are you sure you want to decline this invitation? You can re-join at any time by entering the show ID or accepting an invite from an existing host.",
        icon: "warning",
        buttons: true,
        dangerMode: true
    })
    .then((choice) => {
        if (!choice) throw new Error('User chose to preserve host.');
    })
    .then(() => {
        return axios.put('/api/v1/shows/'+showID+'/join', {
            token: $('input[name="token"]').val(),
            cancel: true
        })
    })
    .then(() => {
        window.location.href = '/shows';
    })
    .catch((err) => {});
}
