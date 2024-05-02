<?php
// Gets used for listing pages within sub directories such as 'Comprehensive Reference Guide'
require_once('Docs.php');
$docs = new Docs;
$url_string = str_replace(BASE_URL, '', current_url());
$dir_path = APPPATH.'trongate-docs/'.$url_string;
$data['html_files'] = $docs->get_html_files_alpha($dir_path);

foreach ($data['html_files'] as $key => $value) {
	$data['html_files'][$key]['page_label'] = $value['page_label'].'()';
}

$reduced_url = str_replace(BASE_URL, '', current_url());
$reduced_url = rtrim($reduced_url, '/');
$url_segments = explode('/', $reduced_url);
$num_segments = count($url_segments);

$last_segment = $url_segments[$num_segments-1];

$data['page_headline'] = $last_segment;
$data['page_headline'] = str_replace('_', ' ', $data['page_headline']);

$data['table_of_contents'] = $docs->fetch_table_of_contents();
$data['view_file'] = 'docs_index_alt.php';
$data['docs'] = $docs;
$docs->view('docs_template', $data);

// 



/*
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

	if (!isset($url_bits[1])) {
		$view_file_path = APPPATH.'trongate-docs/docs_chapter_intro.html';
		$chapter_name_num = $docs->fetch_chapter_name_num(current_url());

		$data['chapter_name'] = $chapter_name_num['chapter_name'];
		$data['chapter_num'] = $chapter_name_num['chapter_num'];
	} else {
		$target_file_name = $url_bits[1];
		$real_file_name = $docs->get_real_file_name($dir_real_name, $target_file_name);

		if($real_file_name === false) {
			header("location: ".BASE_URL);
			die();
		}

		$view_file_path = APPPATH.'trongate-docs/'.$dir_real_name.'/'.$real_file_name;
	}

}

$data['view_file'] = $view_file_path;
$data['docs'] = $docs;
$docs->view('docs_template', $data);
*/