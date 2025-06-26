window.vc = {
    viewedPosts: []
}
setInterval(function() {
    if (window.vc.viewedPosts.length > 0) {
        markViewedPosts()
    }
}, 10000);

function markViewedPosts() {
    fetch('/mark_viewed_posts', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json', // or 'application/x-www-form-urlencoded'
        },
        body: JSON.stringify(window.vc.viewedPosts), // or new URLSearchParams(new FormData(formElement))
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json(); // or response.text() for plain text
    })
    .then(data => {
        console.log('Посты прочитаны ', window.vc.viewedPosts)
        window.vc.viewedPosts = []
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
function isElementInViewport(el) {
    let rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

function findVisibleElements() {
    let elements = document.querySelectorAll('tr[data-id]:not(.viewed)'); // Замените на селектор ваших элементов
    let visibleElements = [];

    for (let i = 0; i < elements.length; i++) {
        if (isElementInViewport(elements[i])) {
            visibleElements.push(elements[i].getAttribute('data-id'));
            elements[i].classList.add('viewed')
            let views = elements[i].querySelector('td.views')
            views.innerText = parseInt(views.textContent) + 1
        }
    }

    return visibleElements;
}

window.addEventListener('scroll', function() {
    let visible = findVisibleElements();
    window.vc.viewedPosts = [...new Set([...window.vc.viewedPosts, ...visible])];
});

