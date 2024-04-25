function goToDocsHome(baseUrl) {
    window.location.href = baseUrl;
}

document.addEventListener("DOMContentLoaded", function() {
    // Select all <code> elements
    const codeBlocks = document.querySelectorAll('code');

    // Iterate through all collected <code> elements
    codeBlocks.forEach(function(code) {
    	console.log(code);
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
        	alertHeadline = 'Did You Know?';
        	alertIcon = '<i class="fa fa-info-circle"></i>';
        }

        if(alertEl.classList.contains('alert-warning')) {
        	alertHeadline = 'Warning!';
        	alertIcon = '<i class="fa fa-exclamation-circle"></i>';
        }

        if(alertEl.classList.contains('alert-success')) {
        	alertHeadline = 'Top Tip';
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

        console.log(iframeEl.outerHTML);
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