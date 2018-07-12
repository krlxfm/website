function setListeners() {
    $('.list-group-item[data-track-id]').click(showModal);
}

function showModal(e) {
    e.preventDefault();
    $("#show-title-modal-label, #create-show").text('Create '+$(this).data('trackName')+' Show');
    $(".title-modal-field").text($(this).data('trackTitle').toLowerCase());
    $("#track-input").val($(this).data('trackId'));
    $("#show-title-modal").modal('show');
}

$(setListeners);
