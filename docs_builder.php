<?php
include('Docs.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="http://localhost/fi2/css/trongate.css">
	<title>Document</title>
</head>
<body>
	<div class="container">
		<h1 class="text-center">Docs Builder</h1>
		<p class="text-center"><button onclick="startBuilding()">Start</button></p>			
	</div>
<script>
let chaptersDone = 0;

function buildChapterDir(params, chapterCounter) {
	params.chapterNum = chapterCounter + 1;
	const targetUrl = 'http://localhost/trongate-docs/docs_builder_chapters.php';

	console.log('now posting to ' + targetUrl);
	console.log(JSON.stringify(params));
	return;

	const http = new XMLHttpRequest();
	http.open('post', targetUrl);
	http.setRequestHeader('Content-type', 'application/json');
	http.send(JSON.stringify(params));
	http.onload = function() {
		console.log(http.status);
		console.log(http.responseText);
	}

}

function startBuilding() {
    // Fetch chapter titles from the REAL docs.

	const targetUrl = 'http://localhost/trongate_live5/docs/get_chapters';
	const http = new XMLHttpRequest();
	http.open('get', targetUrl);
	http.setRequestHeader('Content-type', 'application/json');
	http.send();
	http.onload = function() {
		const allChapters = JSON.parse(http.responseText);

		Object.entries(allChapters).forEach(([key, params]) => {

			chaptersDone++;
			if (chaptersDone === 1) {
				console.log('chapters done is ' + chaptersDone);
				buildChapterDir(params, chaptersDone);
			}
			
		});

	}

}




// Loop through all of the chapter titles and...

		// foreach chapter_title ->. {
			// Create an appropriately named directory that represents the chapter.

			// THEN Inside each chapter folder.  ->.  extract HTML for page content (view, true)   -> build .html file
        //}

</script>













</body>
</html>