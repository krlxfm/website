function setListeners() {
    $("#changes-saved-item").hide();
    $(".mc-toolbar-footer a").click(submitFormBeforeContinuing);
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
    console.log(requestData);

    axios.patch('/api/v1/shows/'+showID, requestData)
    .then((response) => {
        $("#changes-saved-item").show();
        $("#changes-saved-item").fadeOut(2000);
        console.log(response);
    })
    .catch((error) => {
        console.log(error);
        if (error.response) {
            console.log(error.response.data.errors);
        }
    });
}

function submitFormBeforeContinuing(e) {
    e.preventDefault();
}

$(setListeners);
