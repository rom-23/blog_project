const $ = require('jquery');
$(document).ready(function() {
    // Prototype CollectionType JQUERY - > MULTISELECT FORM
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
