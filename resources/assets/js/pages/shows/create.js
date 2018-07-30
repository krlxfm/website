function setListeners() {
    $('.list-group-item[data-track-id]').click(showModal);
}

function showModal(e) {
    e.preventDefault();
    $("#show-title-modal-label, #create-show").text('Create '+$(this).data('trackName')+' Show');
    $("#track-input").val($(this).data('trackId'));
    $("#show-title-modal").modal('show');
}

$(setListeners);
