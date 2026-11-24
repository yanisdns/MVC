// Scripts de l'administration

// Confirmation avant les actions de suppression : les formulaires portent un attribut data-confirm
document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

// Apercu de l'image choisie avant l'envoi
var coverInput = document.getElementById('cover');
var coverPreview = document.getElementById('cover-preview');

if (coverInput && coverPreview) {
    coverInput.addEventListener('change', function () {
        var file = coverInput.files[0];
        if (!file || file.type.indexOf('image/') !== 0) {
            coverPreview.classList.add('d-none');
            return;
        }
        coverPreview.src = URL.createObjectURL(file);
        coverPreview.classList.remove('d-none');
    });
}

// Boutons d'aide a la saisie : ajoutent "## " ou "- " au debut de la ligne courante
var content = document.getElementById('content');

document.querySelectorAll('[data-insert]').forEach(function (button) {
    button.addEventListener('click', function () {
        if (!content) {
            return;
        }
        var prefix = button.dataset.insert;
        var start = content.selectionStart;
        var lineStart = content.value.lastIndexOf('\n', start - 1) + 1;
        content.value = content.value.slice(0, lineStart) + prefix + content.value.slice(lineStart);
        content.focus();
        content.selectionStart = content.selectionEnd = start + prefix.length;
    });
});
