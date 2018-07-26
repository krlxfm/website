$(document).ready(function() {
    $("#changes-saved-item").hide();
    $("#scheduling-form input, #scheduling-form textarea, #scheduling-form select").change(saveData);
})

function saveData() {
    var data = $("#scheduling-form").serializeArray();
    var requestData = {classes: []};
    data.forEach((item) => {
        if(item.name == 'classes') {
            requestData.classes.push(item.value);
        } else if(item.name.indexOf('.') == -1) {
            requestData[item.name] = item.value;
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
        $("#changes-saved-item").show();
        $("#changes-saved-item").fadeOut(2000);
    })
    .catch((error) => {
        console.error(error.response.data.errors);
    });
}
