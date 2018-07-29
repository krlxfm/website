function warnAndDelete(index, name, email, isMe) {
    removeAlert(name, isMe)
    .then((willDelete) => {
        if (!willDelete) throw new Error('User chose to preserve host.');
    })
    .then(() => {
        return axios.patch('/api/v1/shows/'+showID+'/hosts', { remove: [email] })
    })
    .then((response) => {
        window.participants.splice(index, 1);
        return removeCompleteAlert(name);
    })
    .then(() => {
        if(isMe) window.location.href = '/shows';
    })
    .catch((err) => {});
}

function removeAlert(name, isMe) {
    if(isMe) {
        return swal({
            title: "Leave this show?",
            text: "Are you sure you want to leave this show? You will need to accept an invitation from a remaining participant in order to re-join. If you're the last participant on this show, it will be deleted.",
            icon: "warning",
            buttons: true,
            dangerMode: true
        })
    }
    return swal({
        title: "Remove "+name+"?",
        text: name+" will be immediately removed from the show, and will need to accept a new invitation in order to re-join.",
        icon: "warning",
        buttons: true,
        dangerMode: true
    })
}

function removeCompleteAlert(name) {
    return swal({
        title: "Done!",
        text: name+" has been removed from the show.",
        icon: "success",
    })
}

$(function() {
    $("#changes-saved-item").hide();
});

function clickNextButton(target) {
    window.location.href = $(target).data('destination');
}
