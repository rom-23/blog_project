const searchBar = document.getElementById('search_bar');
const twigJsonData = document.querySelector('.tableList').dataset.development;
const jsonToArray = JSON.parse(twigJsonData);
const objJson = jsonToArray.map(function (obj) {
    return {
        'title'   : obj,
        'section' : obj.section,
        'posts'   : obj.posts,
        'created' : obj.createdAt
    };
});

searchBar.addEventListener('keyup', e => {
    const searchString = e.target.value;
    const filteredDevelopment = objJson.filter(development => {
        return development.title.title.includes(searchString);
    });
    console.log(filteredDevelopment);
});
(function () {
    function Pagination() {
        const prevButton = document.getElementById('button_prev');
        const nextButton = document.getElementById('button_next');
        const clickPageNumber = document.querySelectorAll('.clickPageNumber');
        const listingTable = document.getElementById('listingTable');
        let current_page = 1;
        let records_per_page = 10;
        this.init = function () {
            changePage(1);
            pageNumbers();
            selectedPage();
            clickPage();
            addEventListeners();
        };
        let addEventListeners = function () {
            prevButton.addEventListener('click', prevPage);
            nextButton.addEventListener('click', nextPage);
        };
        let selectedPage = function () {
            let page_number = document.getElementById('page_number').getElementsByClassName('clickPageNumber');
            for (let i = 0; i < page_number.length; i++) {
                if (i === current_page - 1) {
                    page_number[i].style.opacity = '1.0';
                } else {
                    page_number[i].style.opacity = '0.5';
                }
            }
        };
        let checkButtonOpacity = function () {
            current_page === 1 ? prevButton.classList.add('opacity') : prevButton.classList.remove('opacity');
            current_page === numPages() ? nextButton.classList.add('opacity') : nextButton.classList.remove('opacity');
        };
        let createTable = function () {
            let thead = listingTable.createTHead();
            let row = thead.insertRow();
            let data = Object.keys(objJson[0]);
            for (let key of data) {
                let th = document.createElement('th');
                let text = document.createTextNode(key);
                th.appendChild(text);
                row.appendChild(th);
            }
        };
        let changePage = function (page) {
            if (page < 1) {
                page = 1;
            }
            if (page > (numPages() - 1)) {
                page = numPages();
            }
            listingTable.innerHTML = '';
            createTable();
            for (let i = (page - 1) * records_per_page; i < (page * records_per_page) && i < objJson.length; i++) {
                let row = listingTable.insertRow();
                let cellTitle = row.insertCell();
                let cellSection = row.insertCell();
                let cellPosts = row.insertCell();
                let cellCreatedAt = row.insertCell();
                let devLink = document.createElement('a');
                devLink.innerHTML = objJson[i].title.title;
                devLink.setAttribute('href', '/symfony/development/' + objJson[i].title.id);
                let sectionLink = document.createElement('a');
                sectionLink.innerHTML = objJson[i].section.title;
                sectionLink.setAttribute('href', '/symfony/development/section/' + objJson[i].section.id);
                let posts = document.createTextNode(objJson[i].title.posts.length);
                let createdAt = document.createTextNode(new Date(objJson[i].created).toLocaleDateString('en-us', {
                    weekday : 'short',
                    year    : 'numeric',
                    month   : 'short',
                    day     : 'numeric'
                }));
                cellTitle.appendChild(devLink);
                cellSection.appendChild(sectionLink);
                cellPosts.appendChild(posts);
                cellPosts.className += ' text-center';
                cellCreatedAt.appendChild(createdAt);
                cellCreatedAt.className += ' fst-italic text-muted text-center';
                insertTableLink(devLink, sectionLink);
            }
            checkButtonOpacity();
            selectedPage();
        };
        let insertTableLink = function (DevLink, sectionLink) {
            DevLink.addEventListener('click', function (event) {
                event.preventDefault();
                const route = this.getAttribute('href');
                return result(route);
            });
            sectionLink.addEventListener('click', function (event) {
                event.preventDefault();
                const route = this.getAttribute('href');
                return result(route);
            });
        };
        let result = function (route) {
            const view = document.querySelector('#display-view');
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
        };
        let prevPage = function () {
            if (current_page > 1) {
                current_page--;
                changePage(current_page);
            }
        };
        let nextPage = function () {
            if (current_page < numPages()) {
                current_page++;
                changePage(current_page);
            }
        };
        let clickPage = function () {
            document.addEventListener('click', function (e) {
                if (e.target.nodeName === 'SPAN' && e.target.classList.contains('clickPageNumber')) {
                    current_page = e.target.textContent;
                    changePage(current_page);
                }
            });
        };
        let pageNumbers = function () {
            let pageNumber = document.getElementById('page_number');
            pageNumber.innerHTML = '';

            for (let i = 1; i < numPages() + 1; i++) {
                pageNumber.innerHTML += "<span class='clickPageNumber'>" + i + '</span>';
            }
        };
        let numPages = function () {
            return Math.ceil(objJson.length / records_per_page);
        };
    }

    let pagination = new Pagination();
    pagination.init();
})();
