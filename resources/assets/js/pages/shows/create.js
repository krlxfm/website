function setListeners() {
    $('.list-group-item[data-track-id]').click(showModal);
}

function showModal(e) {
    e.preventDefault();
    console.log($(this).data('trackId'));
}

$(setListeners);
