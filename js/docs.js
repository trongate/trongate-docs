const delayTime = 10;

function fetchFeatureItemsInfo() {
    const targetFeatureRefs = [];
    const featureItems = document.querySelectorAll('.feature-item');
    featureItems.forEach(targetEl => {
        const featureRefSpan = targetEl.querySelector('.feature-ref');
        const targetFeatureRef = pluckFeatureRef(featureRefSpan);
        targetFeatureRefs.push(targetFeatureRef)
    });

    const params = {
        targetFeatureRefs
    }

    const targetUrl = baseUrl + 'docs_api.php';
    const http = new XMLHttpRequest();
    http.open('post', targetUrl);
    http.setRequestHeader('Content-type', 'application/json');

console.log(targetUrl);
console.log(JSON.stringify(params));
    http.send(JSON.stringify(params));

    http.onload = function() {

        if (http.status === 200) {

            setTimeout(() => {
                populateFeatureDescriptions(http.responseText);
            }, delayTime);

        }
    }
}

function populateFeatureDescriptions(responseText) {
    const obj = JSON.parse(responseText);

    // Convert the object to an array of key-value pairs
    const keyValuePairArray = Object.entries(obj);
    const blinkingEls = document.querySelectorAll('#feature-ref-list li .blink');

    for (var i = 0; i < keyValuePairArray.length; i++) {
        const row = keyValuePairArray[i];
        const blinkingElParent = blinkingEls[i].parentNode;
        blinkingEls[i].remove();
        
        if (row[1] === 'fail') {
            blinkingElParent.innerText = 'Description not available.';
            blinkingElParent.style.color = 'red';
        } else {
            blinkingElParent.innerHTML = row[1];
        }

    }

}

function getDataClassValue(element) {
    // Check if the element exists and has the 'data-class' attribute
    if (element && element.hasAttribute('data-class')) {
        return element.getAttribute('data-class');
    } else {
        return false;
    }
}

function pluckFeatureRef(featureRefEl) {
    // Attempt to return an item from existingFeatureRefs that can be matched with the element
    let matchingFeatureRef = false;

    let featureRefName = featureRefEl.innerText;
    featureRefName = featureRefName.replace('()', '');
    const containingClass = getDataClassValue(featureRefEl);

    existingFeatureRefs.forEach(existingFeatureRef => {
        
        if (existingFeatureRef.name === featureRefName) {
            if (containingClass === false) {
                matchingFeatureRef = existingFeatureRef;
            } else {
                const existingFeatureRefDir = existingFeatureRef['feature_ref_dir'];
                const featureRefDirLc = existingFeatureRefDir.toLowerCase();
                if (featureRefDirLc.includes(containingClass)) {
                    matchingFeatureRef = existingFeatureRef;
                }
            }
        }

    });

    return matchingFeatureRef;
 
}

function buildFeatureRefs() {
    const featureRefs = document.querySelectorAll('.feature-ref');

    featureRefs.forEach(featureRefEl => {

        const refName = featureRefEl.innerHTML.replace(/[()]/g, '');
        const targetFeatureRef = pluckFeatureRef(featureRefEl);

        if (targetFeatureRef === false) {
            featureRefEl.classList.remove('feature-ref');
            featureRefEl.style.fontWeight = 'bold';
        } else {

            // Build a 'more info' button
            const btn = document.createElement('button');
            btn.innerHTML = '<i class="fa fa-info-circle"></i>';
            btn.setAttribute('type', 'button');
            btn.setAttribute('class', 'alt');
            const onClickValue = 'initOpenInfo(\'' + JSON.stringify(targetFeatureRef) + '\')';
            btn.setAttribute('onclick', onClickValue);            
            featureRefEl.appendChild(btn);
        }

    });

}

function initOpenInfo(targetFeatureParams) {
    openModal('temp-modal');

    const targetUrl = baseUrl + 'docs_api.php';

    const http = new XMLHttpRequest();
    http.open('post', targetUrl);
    http.setRequestHeader('Content-type', 'application/json');
    http.send(targetFeatureParams);

        http.onload = function() {

            if (http.status === 200) {

                const targetModalBody = document.querySelector('.modal-body');
                while(targetModalBody.firstChild) {
                    targetModalBody.removeChild(targetModalBody.firstChild);
                }

                const results = JSON.parse(http.responseText);

                targetModalBody.innerHTML = results[0];

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

function goToDocsHome(url) {
    // Redirect to the specified URL
    window.location.href = url;
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

document.addEventListener('scroll', function() {
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    // "window.innerHeight" gives the height of the viewport
    // If scrolled more than one viewport height, show the button
    if (window.scrollY > window.innerHeight) {
        scrollToTopBtn.style.display = 'block';
    } else {
        scrollToTopBtn.style.display = 'none';
    }
});

document.getElementById('scrollToTopBtn').addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth' // Enable smooth scrolling
    });
});

buildFeatureRefs();