function setListeners() {
    $('[data-action="remove-dj"]').click(warnAndDelete);
    $("#changes-saved-item").hide();
}

function warnAndDelete(e) {
    var button = $(e.delegateTarget);
    removeAlert(button.data('name'))
    .then((willDelete) => {
        if (!willDelete) throw new Error('User chose to preserve host.');
    })
    .then(() => {
        return axios.patch('/api/v1/shows/'+showID+'/hosts', { remove: [button.data('email')] })
    })
    .then((response) => {
        return removeCompleteAlert(button.data('name'))
    })
    .catch((err) => {});
}

function removeAlert(name) {
    return swal({
        title: "Remove "+name+"?",
        text: name+" will be immediately removed from the show, and will need to accept a new invitation in order to re-join.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
}

function removeCompleteAlert(name) {
    return swal({
        title: "Done!",
        text: name+" has been removed from the show.",
        icon: "success",
    })
}

$(setListeners);
