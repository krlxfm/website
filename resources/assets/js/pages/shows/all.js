$(document).ready(function() {
    $("#remind").click(showRemindAlert);
});

function showRemindAlert() {
    swal({
        title: 'Remind incomplete shows?',
        text: 'Would you like to send email reminders to the ' + $('[data-status="incomplete"]').length + ' incomplete shows? Emails will be sent immediately, so please take care not to spam this button.',
        buttons: true
    })
    .then((choice) => {
        if(!choice) throw new Error();
        axios.post('/api/v1/shows/remind', {
            term_id: termID
        })
        .catch((err) => {
            swal({
                title: 'Reminders Not Sent',
                text: 'Due to the following error, reminder emails were not sent: ',
                icon: 'error'
            })
        })
    })
    .catch((err) => {});
}
