function setListeners() {
    $(".mc-toolbar-footer a").click(submitFormBeforeContinuing);
}

function submitFormBeforeContinuing(e) {
    e.preventDefault();
}

$(setListeners);
