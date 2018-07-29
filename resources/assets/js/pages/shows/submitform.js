function submitForm() {
    var field = $(this);
    var requestData = {};
    var components = field.attr('name').split('.');
    if (field.data('cast') && field.data('cast') == 'array') {
        var data = $('[name="'+components[0]+'"]:checked');
        requestData[components[0]] = [];
        data.each(function(index, item) {
            var itemName = $(item).val();
            requestData[components[0]].push(itemName);
        })
    } else if (components.length == 1) {
        requestData[field.attr('name')] = field.val();
    } else if (components.length == 2) {
        var data = $('[name^="'+components[0]+'."]');
        requestData[components[0]] = {};
        data.each(function(index, item) {
            var itemName = $(item).attr('name').split('.')[1];
            requestData[components[0]][itemName] = $(item).val();
        })
    }

    return sendUpdateRequest(showID, requestData);
}

function sendUpdateRequest(showID, data) {
    axios.patch('/api/v1/shows/'+showID, data)
    .then((response) => {
        removeValidationErrors(data);
        $("#changes-saved-item").show();
        $("#changes-saved-item").fadeOut(2000);
        if(showValidationErrors) {
            $("#next-button").prop('disabled', $(".is-invalid").length > 0);
        }
    })
    .catch((error) => {
        showErrors(error.response.data.errors);
    });
}

function removeValidationErrors(data, prefix = '') {
    Object.keys(data).forEach((field) => {
        if(typeof data[field] === 'object') {
            removeValidationErrors(data[field], field+'.');
        } else {
            $('[name="'+prefix+field+'"] ~ div.invalid-feedback').remove();
            $('[name="'+prefix+field+'"]').removeClass('is-invalid');
        }
    });
}

function showErrors(errors) {
    console.error(JSON.stringify(errors));
    if(errors && Object.keys(errors).length > 0 && showValidationErrors) {
        $("#next-button").prop('disabled', true);
        showValidationErrors(errors);
    }
}

$(document).ready(function() {
    showErrors(window.validationErrors);
})
