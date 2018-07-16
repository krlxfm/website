function setListeners() {
    $("#changes-saved-item").hide();
    $("input, textarea").change(submitForm)
}

function submitForm() {
    var data = $("#content-form").serializeArray();
    var requestData = {};
    data.forEach((item) => {
        if(item.name.indexOf('.') == -1) {
            requestData[item.name] = item.value
        } else {
            var components = item.name.split('.');
            if (components[0] in requestData === false) requestData[components[0]] = {};
            requestData[components[0]][components[1]] = item.value;
        }
    });
    delete requestData._method;
    delete requestData._token;

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

        field.addClass('is-invalid');
        field.after('<div class="invalid-feedback">'+message+'</div>');
    })
}

$(setListeners);
