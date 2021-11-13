const $ = require('jquery');
$(document).ready(function() {
    // Create Prototype CollectionType JQUERY - > MULTISELECT FORM
    $('.add-another-collection-widget').click(function (e) {
        let list = $('#images-fields-list');
        let counter = list.data('widget-counter') | list.children().length;
        let newWidget = list.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, counter);
        counter++;
        list.data('widget-counter', counter);
        let newElem = $(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);
        newElem.append('<a href="#" class="remove-tag" style="color: navajowhite;text-decoration: none;">remove</a>');
        $('.remove-tag').click(function(e) {
            e.preventDefault();
            $(this).parent().remove();
        });
    });
});

window.setTimeout(function () {
    $('.alert-message').fadeTo(500, 0).slideUp(500, function () {
        $(this).remove();
    });
}, 2000);

// ADMIN NOTE PROTOTYPE in JS es6
const newNoteItem = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
    const noteItem = document.createElement('div');
    noteItem.classList.add('col-6');
    noteItem.innerHTML = collectionHolder.dataset.prototype.replace(/__name__/g, collectionHolder.dataset.index);
    noteItem.querySelector('.btn-remove-note').addEventListener('click', () => {
        return noteItem.remove();
    });
    collectionHolder.appendChild(noteItem);
    collectionHolder.dataset.index++;
};

document.querySelectorAll('.btn-remove-note').forEach(btn => {
    return btn.addEventListener('click', (e) => {
        return e.currentTarget.closest('.col-6').remove();
    });
});

document.querySelectorAll('.btn-new-note').forEach(btn => {
    return btn.addEventListener('click', newNoteItem);
});

// DEV File Upload
const newFileItem = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
    const fileItem = document.createElement('div');
    fileItem.classList.add('col');
    fileItem.innerHTML = collectionHolder.dataset.prototype.replace(/__name__/g, collectionHolder.dataset.index);
    fileItem.querySelector('.btn-remove-file').addEventListener('click', () => {
        return fileItem.remove();
    });
    collectionHolder.appendChild(fileItem);
    collectionHolder.dataset.index++;
};

document.querySelectorAll('.btn-remove-file').forEach(btn => {
    return btn.addEventListener('click', (e) => {
        return e.currentTarget.closest('.row').remove();
    });
});

document.querySelectorAll('.btn-new-file').forEach(btn => {
    return btn.addEventListener('click', newFileItem);
});

// MODEL File Upload
const newImageItem = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
    const fileItem = document.createElement('div');
    fileItem.classList.add('col');
    fileItem.innerHTML = collectionHolder.dataset.prototype.replace(/__name__/g, collectionHolder.dataset.index);
    fileItem.querySelector('.btn-remove-image').addEventListener('click', () => {
        return fileItem.remove();
    });
    collectionHolder.appendChild(fileItem);
    collectionHolder.dataset.index++;
};

document.querySelectorAll('.btn-remove-image').forEach(btn => {
    return btn.addEventListener('click', (e) => {
        return e.currentTarget.closest('.row').remove();
    });
});

document.querySelectorAll('.btn-new-image').forEach(btn => {
    return btn.addEventListener('click', newImageItem);
});

// DEV & USER POST PROTOTYPE in JS es6
document.querySelectorAll('.btn-remove-post').forEach(btn => {
    return btn.addEventListener('click', (e) => {
        return e.currentTarget.closest('.col-6').remove();
    });
});

// MODEL OPINION PROTOTYPE in JS es6
document.querySelectorAll('.btn-remove-opinion').forEach(btn => {
    return btn.addEventListener('click', (e) => {
        return e.currentTarget.closest('.col-6').remove();
    });
});
