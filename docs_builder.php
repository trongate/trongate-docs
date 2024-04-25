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
		<h2 class="text-center"></h2>
		<p class="text-center"><button onclick="startBuilding()">Start</button></p>			
	</div>
<script>
let targetChapterNum = <?= $_GET['chapter'] ?>;

let chaptersDone = 0;

function buildChapterDir(params, chapterCounter) {
	params.chapterNum = targetChapterNum;
	const targetUrl = 'http://localhost/trongate-docs/docs_builder_chapters.php';

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
			if (chaptersDone === targetChapterNum) {
				console.log('chapters done is ' + chaptersDone);
				buildChapterDir(params, chaptersDone);
			}
			
		});

	}

}

function estTargetDir() {

    let chapterNumber = 0;
	const targetUrl = 'http://localhost/trongate_live5/docs/get_chapters';
	const http = new XMLHttpRequest();
	http.open('get', targetUrl);
	http.setRequestHeader('Content-type', 'application/json');
	http.send();
	http.onload = function() {

		const allChapters = JSON.parse(http.responseText);

		Object.entries(allChapters).forEach(([key, params]) => {

			chapterNumber++;
			if (chapterNumber === targetChapterNum) {
			    const subHeadlineEl = document.querySelector('h2');
			    subHeadlineEl.innerText = params.chapter_title;
			}
			
		});

	}

}

window.addEventListener('load', (ev) => {
	estTargetDir();
});
</script>













</body>
</html>