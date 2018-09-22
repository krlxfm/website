$(document).ready(function() {
    $("#remind").click(showRemindAlert);
});

function showRemindAlert() {
    swal({
        title: 'Remind incomplete shows?',
        text: 'Would you like to send email reminders to the ' + $('[data-status="incomplete"]').length + ' incomplete shows? Emails will be sent immediately, so please take care not to spam this button.',
        buttons: true,
        icon: 'warning'
    })
    .then((choice) => {
        if(!choice) throw new Error();
        axios.post('/api/v1/shows/remind', {
            term_id: termID
        })
        .then(() => {
            swal({
                title: 'Reminders Sent',
                text: 'All incomplete shows have been reminded!',
                icon: 'success'
            })
        })
        .catch((err) => {
            swal({
                title: 'Reminders Not Sent',
                text: 'An error occurred and reminders were not sent: ' + err.response.data.message,
                icon: 'error'
            })
        })
    })
    .catch((err) => {});
}
