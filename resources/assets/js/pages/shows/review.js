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
        axios.put('/api/v1/shows/'+showID+'/submitted', {
            submitted: true
        })
        .then((response) => {
            console.log(response);
        })
        .catch((err) => {
            showErrors(err.response.data.errors);
        })
    })
    .catch((err) => {});
}

function showErrors(errors) {
    var list = document.createElement('ul');
    for(var field in errors) {
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
