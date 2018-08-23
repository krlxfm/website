exports.showModal = function () {
    $("#publish").modal('show');
};

exports.publishDraft = function () {
    if(app.controlMessages.warnings.length > 0) {
        swal({
            icon: 'warning',
            title: 'Ignore warning?',
            text: 'The schedule currently has ' + app.controlMessages.warnings.length + ' active warning(s). Are you sure you want to publish the schedule with these warnings?',
            buttons: true,
            dangerMode: true
        })
        .then(result => {
            if(result) {
                startPublication(false);
            }
        });
    } else {
        startPublication(false);
    }
};

function startPublication(isFinal) {
    $("#publish").modal('hide');
    $("#publish-progress-bar").removeClass('bg-success');
    app.progress = 0;
    app.currentItem = 'Connecting to Google Calendar...';
    axios.patch('/api/v1/schedule/publish', {
        'publish': Object.keys(app.diffs),
        'final': isFinal
    });
    window.publishStatusTimer = setInterval(app.checkPublishStatus, 1500);
    $("#publishStatus").modal('show');
}

exports.checkPublishStatus = function () {
    axios.get('/api/v1/schedule/publish')
    .then(response => {
        if(response.status == 200) {
            app.progress = response.data.position;
            const show = window.showList[response.data.show];
            const showActions = {'new': 'Publishing', 'mv': 'Updating', 'rm': 'Removing'};
            app.currentItem = showActions[app.diffs[show.id]] + ' ' + show.title + '...';
        } else if (app.progress != 0 || Object.keys(app.diffs).length == 1) {
            app.progress = 1 + Object.keys(app.diffs).length;
            app.currentItem = 'Finished!';
            $("#publish-progress-bar").addClass('bg-success');
            window.publishStatusTimer = null;
        }
    })
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
