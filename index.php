<?php
require_once('Docs.php');
$docs = new Docs;
$data['table_of_contents'] = $docs->return_table_of_contents();
$view_file_path = $docs->get_view_file_path($data['table_of_contents']);

$data['view_file'] = $view_file_path;
$docs->view('docs_template', $data);