$(document).ready(function() {
    $("id-search").removeClass('is-invalid');
    $("#id-search-form").submit(goToShow);
});

function goToShow(e) {
    e.preventDefault();
    var showID = $("#id-search").val().toUpperCase();
    axios.get('/api/v1/shows/'+showID)
    .then((response) => {
        $("#id-search").removeClass('is-invalid');
        window.location.href = '/shows/join/' + showID;
    })
    .catch((error) => {
        if(error.response.status == 404) {
            $("#invalid-id").text(showID);
            $("#id-search").addClass('is-invalid');
        }
    })
}
