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

		if ($action === 'attemptEmbelishPage') {
			$current_url = $params->currentUrl;

			$docs = new Docs;
			$table_of_contents = $docs->fetch_table_of_contents();

			$response['next_prev_btns'] = get_next_prev($current_url, $table_of_contents);
			$response['breadcrumbs'] = fetch_breadcrumbs_array($current_url, $table_of_contents);
			http_response_code(200);
			echo json_encode($response);
		}

	} else {
		http_response_code(400);
		echo 'Bad request.';
		die();
	}

}

function get_next_prev($current_url, $table_of_contents) {
    $prev = false;
    $next = false;

	$reduced_current_url = str_replace(BASE_URL, '', $current_url);
	$url_bits = explode('/', $reduced_current_url);

	$first_segment = $url_bits[0] ?? '';
	$second_segment = $url_bits[1] ?? '';

	if ($first_segment === 'introduction') {
		return false;
	}

	// Add the intro pages to the start of the table of contents.
	
	foreach ($table_of_contents as $key => $value) {
		$chapter_name = $value['dir_label'];
		$chapter_pages = $value['pages'] ?? [];
		$modified_chapter_pages = [];

		if (isset($chapter_pages[0])) {
			$reduced_page_url = str_replace(BASE_URL, '', $chapter_pages[0]['page_url']);
			$page_url_bits = explode('/', $reduced_page_url);
			$chapter_first_segment = $page_url_bits[0];
		} else {
			$chapter_first_segment = substr($value['dir_name'], 4);
			$chapter_first_segment = strtolower($chapter_first_segment);
		}

		$row_data['page_label'] = $chapter_name;
		$row_data['page_url'] = BASE_URL.$chapter_first_segment;
		$row_data['page_name'] = '';
		$modified_chapter_pages[] = $row_data;

		foreach($chapter_pages as $chapter_page) {
			$modified_chapter_pages[] = $chapter_page;
		}

		$table_of_contents[$key]['pages'] = $modified_chapter_pages;
	}
	
	$next_prev['prev'] = get_prev_url($current_url, $table_of_contents);
	$next_prev['next'] = get_next_url($current_url, $table_of_contents);
	return $next_prev;
}

function get_next_url($current_url, $table_of_contents) {
	$next_url = false;
	$found_page = false;

	foreach($table_of_contents as $key => $value) {
		$chapter_pages = $value['pages'];
		foreach($chapter_pages as $page_key => $page_value) {
			$page_url = $page_value['page_url'];

			if ($found_page === true) {
				$next_url = $page_value;
				return $next_url;
			}

			if ($page_url === $current_url) {
				$found_page = true;
			}
		}
	}

	return $next_url;
}

function get_prev_url($current_url, $table_of_contents) {
	$prev = false;

	foreach($table_of_contents as $key => $value) {
		$chapter_pages = $value['pages'];
		foreach($chapter_pages as $page_key => $page_value) {
			$page_url = $page_value['page_url'];
			if ($page_url === $current_url) {
				return $prev;
			}

			$prev = $page_value;
		}
	}

	return $prev;

}

function fetch_breadcrumbs_array($current_url, $table_of_contents) {

	$row_data['label'] = 'Table of Contents';
	$row_data['page_url'] = BASE_URL.'introduction/table_of_contents.html';
	$row_data['active'] = ($current_url === $row_data['page_url']) ? true : false;
	$breadcrumbs_array[] = $row_data;

	$reduced_current_url = str_replace(BASE_URL, '', $current_url);
	$url_bits = explode('/', $reduced_current_url);

	$first_segment = $url_bits[0] ?? '';
	$second_segment = $url_bits[1] ?? '';

	if ($first_segment === 'introduction') {
		return false;
	}

	$found = false; // Have we found the page within the $table_of_contents array?

	if (($first_segment !== '') && ($second_segment !== '')) {
		// Find out which chapter (if any) this page belongs to.
		foreach($table_of_contents as $chapter) {
			$chapter_pages = $chapter['pages'];

			foreach($chapter_pages as $chapter_page) {
				$page_url = $chapter_page['page_url'];
				if ($page_url === $current_url) {
					
					$row_data['label'] = $chapter['dir_label']; // The chapter name
					$row_data['page_url'] = BASE_URL.$first_segment; // The chapter home URL
					$row_data['active'] = ($current_url === $row_data['page_url']) ? true : false;
					$breadcrumbs_array[] = $row_data;

					$row_data['label'] = $chapter_page['page_label'];
					$row_data['page_url'] = $current_url;
					$row_data['active'] = ($current_url === $row_data['page_url']) ? true : false;
					$breadcrumbs_array[] = $row_data;			

				}
			}
		}
	} 

	$num_links = count($breadcrumbs_array);

	if ($num_links === 1) {
		return false;
	} else {
		return $breadcrumbs_array;
	}
	
}

handleRequestedAction();