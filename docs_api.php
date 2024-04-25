<?php
include('Docs.php');


function returnTableOfContents() {
	http_response_code(200);
	$docs = new Docs;
	$table_of_contents = $docs->fetch_table_of_contents();
	http_response_code(200);
	echo json_encode($table_of_contents);
}

function handleRequestedAction() {

	// Get the requested action.
	$posted_params = file_get_contents('php://input');
	$params = json_decode($posted_params);

	// Check if JSON decoding was successful
	if ($params !== null) {
		$action = $params->action ?? false;

		if ($action === 'return table of contents') {
			returnTableOfContents();
		}

	} else {
		http_response_code(400);
		echo 'Bad request.';
		die();
	}

}


handleRequestedAction();