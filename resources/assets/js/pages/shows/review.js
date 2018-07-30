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
        axios.put('/api/v1/shows/'+showID+'/submitted', {
            submitted: true
        })
        .then((response) => {
            return successAlert();
        })
        .then(() => {
            window.location.href = '/shows';
        })
        .catch((err) => {
            showErrors(err.response.data.errors);
        })
    })
    .catch((err) => {});
}

function successAlert() {
    return swal({
        title: "Done!",
        icon: "success",
        text: "The show has been successfully submitted! You can continue to edit this application if needed. A confirmation email is on its way as well."
    })
}

function showErrors(errors) {
    $("tr").removeClass('table-danger');
    var list = document.createElement('ul');
    for(var field in errors) {
        $('tr[data-field="'+field+'"]').addClass('table-danger');
        var errMessage = errors[field][0];
        if(field.indexOf('.') != -1) {
            var components = field.split('.');
            errMessage = errMessage.replace(components[0] + '.', '');
        }
        var item = document.createElement('li');
        var newContent = document.createTextNode(errMessage);
        item.style.textAlign = 'left';
        item.appendChild(newContent);
        list.appendChild(item);
    }

    var errorLength = Object.keys(errors).length;

    return swal({
        title: "Whoops!",
        text: (errorLength == 1 ? 'One error prevented the show from being submitted. Please fix it and try again.' : errorLength + " errors prevented the show from being submitted. Please fix them and try again."),
        content: list,
        icon: "error"
    })
}
