function setListeners() {
    $("#changes-saved-item").hide();
    setStandardListeners('input, textarea');
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

        field.addClass('is-invalid');
        field.siblings('div.invalid-feedback').remove();
        field.after('<div class="invalid-feedback">'+message+'</div>');
    })
}

$(setListeners);
