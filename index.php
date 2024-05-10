<?php
/*
 * Main Entry Point
 * This file serves as the main entry point for all requests. 
 * All requests are redirected here by the .htaccess file to handle routing and processing.
 * This allows for a cleaner, more modular approach to developing features and handling URLs.
 */
require_once 'config.php';
require_once 'docs_helpers.php';
require_once 'Docs.php';
$docs = new Docs;
$docs_contents = $docs->est_docs_contents();

$view_file_path = $docs->return_view_file_path($docs_contents);

if ($view_file_path === '') {
	header("location: ".BASE_URL);
	die();
}

// Prepare an array of existingFeatureRefs
$data['existing_feature_refs'] = $docs_contents->feature_refs;

if (strpos($view_file_path, 'docs_chapter_intro.php') !== false) {
	$data['chapter_intro_data'] = $docs->get_chapter_intro_data($docs_contents);
}

$data['breadcrumbs_array'] = $docs->build_breadcrumbs_array($docs_contents);
$data['next_prev_data'] = $docs->get_next_prev_array($docs_contents);
$data['docs_contents'] = $docs_contents;
$data['view_file'] = $view_file_path;
$docs->view('docs_template', $data);