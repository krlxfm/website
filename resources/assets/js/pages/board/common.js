$(document).ready(function () {
    tinymce.init({
        selector: 'textarea',
        plugins: 'lists link paste',
        menubar: false,
        toolbar: 'cut copy paste pastetext | undo redo | link | formatselect | bold italic underline strikethrough | bullist numlist | removeformat',
        branding: false,
        resize: false,
        height: 200
    });
});
