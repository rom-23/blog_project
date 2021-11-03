const view = document.querySelector('#display-view');

function panelView(route) {
    // eslint-disable-next-line func-style
    const getView = async function () {
        try {
            let response = await fetch(route);
            if (response.ok) {
                view.innerHTML = '';
                let data = await response.json();
                view.innerHTML = data['view'];
            }
        } catch (e) {
            alert(e.message);
        }
    };
    return getView();
}

document.querySelectorAll('.dev_item').forEach(function (link) {
    link.addEventListener('click', function (event) {
        event.preventDefault();
        const route = this.getAttribute('href');
        return panelView(route);
    });
});

window.onload = () => {
    // Gestion des boutons "Supprimer"
    let links = document.querySelectorAll('[data-delete]');
    for (let link of links) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure to remove this file ?')) {
                fetch(this.getAttribute('href'), {
                    method  : 'DELETE',
                    headers : {
                        'X-Requested-With' : 'XMLHttpRequest',
                        'Content-Type'     : 'application/json'
                    },
                    body: JSON.stringify({'_token': this.dataset.token})
                }).then(
                    response => {
                        return response.json();
                    }
                ).then(data => {
                    if (data.success) {
                        this.parentElement.remove();
                    } else {
                        alert(data.error);
                    }
                }).catch(e => {
                    return alert(e);
                });
            }
        });
    }
};
