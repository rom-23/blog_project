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
