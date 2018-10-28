$(document).ready(function () {
    tinymce.init({
        selector: 'textarea',
        plugins: 'lists link',
        menubar: false,
        toolbar: 'cut copy paste | undo redo | link | formatselect | bold italic underline strikethrough | bullist numlist | removeformat',
        branding: false,
        resize: false,
        height: 200
    });
});
