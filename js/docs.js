function attemptEmbelishPage() {

    const currentUrl = getCurrentUrl();
    const targetUrl = baseUrl + 'docs_api.php';

    const params = {
        action: 'attemptEmbelishPage',
        currentUrl
    }

    const http = new XMLHttpRequest();
    http.open('post', targetUrl);
    http.setRequestHeader('Content-type', 'application/json');
    http.send(JSON.stringify(params));
    http.onload = function() {

console.log(http.responseText);

        const reponseObj = JSON.parse(http.responseText);
        const breadcrumbs = reponseObj.breadcrumbs;

        if (breadcrumbs !== false) {
            drawBreadcrumbs(breadcrumbs);
        }

        const nextPrevBtns = reponseObj.next_prev_btns;

        if (nextPrevBtns !== false) {
            drawNextPrevBtns(nextPrevBtns);
        }
    }

}

function drawNextPrevBtns(nextPrevBtns) {
    const prev = nextPrevBtns['prev'];
    const prevUrl = prev.page_url;
    const next = nextPrevBtns['next'];
    const nextUrl = next.page_url;

    const mainContainer = document.querySelector('main');
    const firstMainChild = mainContainer.children[0];
    
    const pageNavBtnsContainer = document.createElement('div');
    pageNavBtnsContainer.setAttribute('class', 'page-nav-btns');

    if (!firstMainChild) {
        return;
    }

    // Insert the pageNavBtnsContainer container into the container before the first child
    const container = firstMainChild.parentNode;
    container.insertBefore(pageNavBtnsContainer, firstMainChild);

    // Build a 'Prev' button for navigating to the previous page.
    const prevBtnDiv = document.createElement('div');
    const prevBtn = document.createElement('a');

    prevBtn.setAttribute('href', prevUrl);
    prevBtn.setAttribute('class', 'button');
    prevBtn.innerHTML = '<i class="fa fa-arrow-circle-left"></i> Prev';
    prevBtnDiv.appendChild(prevBtn);

    pageNavBtnsContainer.appendChild(prevBtnDiv);

    // Build a 'Next' button for navigating to the next page.
    const nextBtnDiv = document.createElement('div');
    const nextBtn = document.createElement('a');
    nextBtn.setAttribute('href', nextUrl);
    nextBtn.setAttribute('class', 'button');
    nextBtn.innerHTML = 'Next <i class="fa fa-arrow-circle-right"></i>';
    nextBtnDiv.appendChild(nextBtn);
    pageNavBtnsContainer.appendChild(nextBtnDiv);

    const ridiculouslyHugeEl = document.querySelector('#ridiculously-huge');

    if (!ridiculouslyHugeEl) {
        // Create a clone of the pageNavBtns element
        const pageNavBtnsClone = pageNavBtnsContainer.cloneNode(true);

        // Append the cloned element to the container
        container.appendChild(pageNavBtnsClone);
        pageNavBtnsClone.style.marginTop = '90px';
    }

}

function drawBreadcrumbs(breadcrumbs) {
    // Select the headline element where the breadcrumbs will be inserted before
    const headlineEl = document.querySelector('h1');

    // Create a breadcrumbs container element
    const breadcrumbsContainer = document.createElement('div');
    breadcrumbsContainer.setAttribute('aria-label', 'Breadcrumb');
    breadcrumbsContainer.setAttribute('class', 'breadcrumbs sm');

    let counter = 0;

    breadcrumbs.forEach(row => {
        const breadcrumbEl = document.createElement('div');
        breadcrumbsContainer.appendChild(breadcrumbEl);

        if ((counter >= 0) && (counter < breadcrumbs.length-1)) {
            const dividerEl = document.createElement('span');
            dividerEl.innerHTML = '&raquo;';
            breadcrumbsContainer.appendChild(dividerEl);
        }

        if (row.active !== true) {
            // Build a link
            const linkEl = document.createElement('a');
            linkEl.setAttribute('href', row.page_url);
            linkEl.innerHTML = row.label;
            breadcrumbEl.appendChild(linkEl);
            counter++;
        } else {
            const spanEl = document.createElement('span');
            spanEl.innerHTML = row.label;
            breadcrumbEl.appendChild(spanEl);
            counter++;
        }

    });

    // Insert the breadcrumbs container into the container before the headline element
    const container = headlineEl.parentNode;
    container.insertBefore(breadcrumbsContainer, headlineEl);
}




function getCurrentUrl() {
    return window.location.href;
}

function goToDocsHome(baseUrl) {
    window.location.href = baseUrl;
}

function initOpenInfo(targetUrl) {
    openModal('temp-modal');

        const http = new XMLHttpRequest();
        http.open('get', targetUrl);
        http.setRequestHeader('Content-type', 'application/json');
        http.send();
        http.onload = function() {
            if (http.status === 200) {

                const targetModalBody = document.querySelector('.modal-body');
                while(targetModalBody.firstChild) {
                    targetModalBody.removeChild(targetModalBody.firstChild);
                }

                targetModalBody.innerHTML = http.responseText;

                // Build a close (modal) button.
                const closeBtnPara = document.createElement('p');
                closeBtnPara.setAttribute('class', 'text-center');
                targetModalBody.appendChild(closeBtnPara);

                const btn = document.createElement('button');
                btn.setAttribute('type', 'button');
                btn.setAttribute('class', 'alt');
                btn.innerText = 'Close Window';
                btn.addEventListener('click', (ev) => {
                    closeModal();
                });

                closeBtnPara.appendChild(btn);

                // Build a close window icon for the top rhs
                const closeDiv = document.createElement('div');
                closeDiv.setAttribute('class', 'text-right');

                const btnSmall = document.createElement('button');
                btnSmall.setAttribute('type', 'button');
                btnSmall.style.marginTop = 0;
                btnSmall.innerHTML = '<i class="fa fa-times"></i>';
                btnSmall.addEventListener('click', (ev) => {
                    closeModal();
                });

                closeDiv.appendChild(btnSmall);
                targetModalBody.insertBefore(closeDiv, targetModalBody.firstChild);
            }
        }




}

document.addEventListener("DOMContentLoaded", function() {
    // Select all <code> elements
    const codeBlocks = document.querySelectorAll('code');

    // Iterate through all collected <code> elements
    codeBlocks.forEach(function(code) {
        // Trim the whitespace and set the trimmed content back
        let newContent = code.innerHTML.trim();
        code.innerHTML = newContent;
    });

    const alertEls = document.querySelectorAll('.alert');
    alertEls.forEach(function(alertEl) {
        const alertBodyInner = alertEl.innerHTML.trim();;

        let alertHeadline = 'Alert';
        let alertIcon = '<i class="fa fa-star"></i>';

        if(alertEl.classList.contains('alert-info')) {
        	alertHeadline = 'Further Information';
        	alertIcon = '<i class="fa fa-info-circle"></i>';
        }

        if(alertEl.classList.contains('alert-warning')) {
        	alertHeadline = 'Warning!';
        	alertIcon = '<i class="fa fa-exclamation-circle"></i>';
        }

        if(alertEl.classList.contains('alert-success')) {
        	alertHeadline = 'Best Practices';
        }

        if(alertEl.classList.contains('alert-danger')) {
        	alertHeadline = 'Danger!';
        	alertIcon = '<i class="fa fa-warning"></i>';
        }

		while(alertEl.firstChild) {
			alertEl.removeChild(alertEl.firstChild);
		}

		const alertHeading = document.createElement('div');
		alertHeading.setAttribute('class', 'alert-heading');
		alertHeading.innerHTML = alertIcon + ' ' + alertHeadline;
		alertEl.appendChild(alertHeading);

		const alertBody = document.createElement('div');
		alertBody.setAttribute('class', 'alert-body');
		alertBody.innerHTML = alertBodyInner;
		alertEl.appendChild(alertBody);

    });

    const iframes = document.querySelectorAll('iframe');
    iframes.forEach(iframeEl => {

        if (!iframeEl.classList.contains('.video')) {
            iframeEl.classList.add('video');

            // Create a div element to act as the container
            const containerDiv = document.createElement('div');
            containerDiv.className = 'video-container';

            // Insert the new div before the iframe in the DOM
            iframeEl.parentNode.insertBefore(containerDiv, iframeEl);

            // Move the iframe inside the new container div
            containerDiv.appendChild(iframeEl);
        }
    });

});

attemptEmbelishPage();