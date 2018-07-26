function setListeners() {
    $("#changes-saved-item").hide();
    $("input, textarea").change(submitForm)
}

function submitForm() {
    var field = $(this);
    var requestData = {};
    var components = field.attr('name').split('.');
    if (components.length == 1) {
        requestData[field.attr('name')] = field.val();
    } else if (components.length == 2) {
        var data = $('[name^="'+components[0]+'."]');
        requestData[components[0]] = {};
        data.each(function(index, item) {
            var itemName = $(item).attr('name').split('.')[1];
            console.log($(item).attr('name').split('.')[1]);
            requestData[components[0]][itemName] = $(item).val();
        })
    }

    axios.patch('/api/v1/shows/'+showID, requestData)
    .then((response) => {
        $("div.invalid-feedback, div.valid-feedback").remove();
        $(".is-invalid").removeClass('is-invalid');
        $("#changes-saved-item").show();
        $("#changes-saved-item").fadeOut(2000);
    })
    .catch((error) => {
        $("div.invalid-feedback, div.valid-feedback").remove();
        $(".is-invalid").removeClass('is-invalid');
        if (error.response) {
            showValidationErrors(error.response.data.errors);
        }
    });
}

function showValidationErrors(rawErrors) {
    var errors = Object.entries(rawErrors);
    errors.forEach((error) => {
        var key = error[0];
        var message = error[1][0];
        var field = $('[name="'+key+'"]');

        if(key.indexOf('.') != -1) {
            var components = key.split('.');
            message = message.replace(components[0] + '.', '');
        }

        if(field.val().length > 0) {
            field.addClass('is-invalid');
            field.after('<div class="invalid-feedback">'+message+'</div>');
        }
    })
}

$(setListeners);
