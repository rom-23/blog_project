document.querySelectorAll('[data-reply]').forEach(element => {
    element.addEventListener('click', function () {
        document.querySelector('#post_parentid').value = this.dataset.id;
        document.querySelector('#post_title').focus();
    });
});
