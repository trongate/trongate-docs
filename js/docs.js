function buildFeatureRefs() {
    const featureRefs = document.querySelectorAll('.feature-ref');

    featureRefs.forEach(featureRefEl => {
        const refName = featureRefEl.innerHTML;
        const featureRefUrl = existingFeatureRefs[refName] || false;
        if (featureRefUrl === false) {
            featureRefEl.classList.remove('feature-ref');
            featureRefEl.style.fontWeight = 'bold';
        } else {

            const fullFeatureRefUrl = baseUrl + refDir + featureRefUrl;

            // Build a 'more info' button
            const btn = document.createElement('button');
            btn.innerHTML = '<i class="fa fa-info-circle"></i>';
            btn.setAttribute('type', 'button');
            btn.setAttribute('class', 'alt');
            btn.setAttribute('onclick', 'initOpenInfo(\'' + fullFeatureRefUrl + '\')');           
            featureRefEl.appendChild(btn);            
        }

    });
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
                    initCloseModal();
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
                    initCloseModal();
                });

                closeDiv.appendChild(btnSmall);
                targetModalBody.insertBefore(closeDiv, targetModalBody.firstChild);
            }
        }

}

function beautifyCodeBlocks() {
    const codeBlocks = document.querySelectorAll('code');

    codeBlocks.forEach(function(code) {
        // Trim the whitespace and set the trimmed content back
        let newContent = code.innerHTML.trim();
        code.innerHTML = newContent;
    });
}

function initCloseModal() {

    const modalBody = document.querySelector('.modal-body');
    while(modalBody.firstChild) {
        modalBody.removeChild(modalBody.firstChild);
    }

    closeModal();

}

document.addEventListener("DOMContentLoaded", function() {
    // Make the <code> elements look beautiful!
    beautifyCodeBlocks();

    // Make the alert divs fantasticola!
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

    // Add some classes into iframes to make em look finger-licking good!
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

    const pageNavBtns = document.querySelectorAll('.page-nav-btns');
    // Add some top margin onto that thing!
    if (pageNavBtns[1]) {
        pageNavBtns[1].style.marginTop = '76px';
    }

});

document.addEventListener('click', function(event) {
    // Check if the clicked element or any of its parents have the class 'signature'
    const clickedEl = event.target;
    const targetOpenModal = document.querySelector('.modal-body .signature');

    if (targetOpenModal) {
        if (!event.target.closest('.modal-body')) {
            initCloseModal();
        }
    }
});

buildFeatureRefs();