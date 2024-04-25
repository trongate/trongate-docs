<?php
require_once('Docs.php');
$docs = new Docs;
$data['table_of_contents'] = $docs->fetch_table_of_contents();

$url_string = str_replace(BASE_URL, '', current_url());

if ($url_string === '') {
	// The view file will be the first file from the first directory.
	$view_file_path = APPPATH.'trongate-docs/';
    $first_dir = $data['table_of_contents'][0]['dir_name'];
    $first_filename = $data['table_of_contents'][0]['pages'][0]['page_name'];
    $view_file_path.= $first_dir.'/'.$first_filename;
} else {
	$url_bits = explode('/', $url_string);

	$target_dir = $url_bits[0];
	$dir_real_name = $docs->get_dir_real_name($target_dir);

	if ($dir_real_name === false) {
		http_response_code(404);
		echo 'page not found';
		die();
	}

	$target_file_name = $url_bits[1];
	$real_file_name = $docs->get_real_file_name($dir_real_name, $target_file_name);
	$view_file_path = APPPATH.'trongate-docs/'.$dir_real_name.'/'.$real_file_name;
}

$data['view_file'] = $view_file_path;
$data['docs'] = $docs;
$docs->view('docs_template', $data);
