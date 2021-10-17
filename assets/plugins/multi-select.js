const $ = require('jquery');
require('select2');
$(document).ready(function() {
    $('.select2').select2({
        tags            : true,
        placeholder     : 'Select an option',
        allowClear      : true,
        tokenSeparators : [',', ' ']
    }).on('change', function (e) {
        // let label = $(this).find('[data-select2-tag=true]');
        console.log($(this).find(':selected'));
    });
    // Prototype CollectionType JQUERY
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

// Prototype CollectionType in JS es6
const newItem = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
    const postItem = document.createElement('div');
    postItem.classList.add('col-4');
    postItem.innerHTML = collectionHolder.dataset.prototype.replace(/__name__/g, collectionHolder.dataset.index);
    postItem.querySelector('.btn-remove-post').addEventListener('click', () => { return postItem.remove(); });
    collectionHolder.appendChild(postItem);
    collectionHolder.dataset.index++;
};

document.querySelectorAll('.btn-remove-post').forEach(btn => { return btn.addEventListener('click', (e) => { return e.currentTarget.closest('.col-4').remove(); }); });

document.querySelectorAll('.btn-new-post').forEach(btn => { return btn.addEventListener('click', newItem); });

window.setTimeout(function() {
    $('.alert-message').fadeTo(500, 0).slideUp(500, function() {
        $(this).remove();
    });
}, 1500);
