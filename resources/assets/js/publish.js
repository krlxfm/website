exports.showModal = function () {
    $("#publish").modal('show');
};

exports.publishDraft = function () {
    $("#publish").modal('hide');
    app.progress = 0;
    app.currentItem = 'Connecting to Google Calendar...';
    $("#publishStatus").modal('show');
};

exports.publishFinal = function () {
    swal({
        icon: 'warning',
        title: 'Are you sure?',
        text: 'You are about to publish the final schedule and lock it to further changes. This will email all hosts with their show details and inform them that schedule changes are no longer being accepted.',
        buttons: true,
        dangerMode: true
    });
};
