<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

$posted_params = file_get_contents('php://input');
$params = json_decode($posted_params);

// Check if JSON decoding was successful
if ($params !== null) {

	require_once 'config.php';
	require_once 'docs_helpers.php';
	require_once 'Docs.php';
	$docs = new Docs;
	$docs_contents = $docs->est_docs_contents();

	if (isset($params->featurePath)) {
		attempt_serve_feature_details($params, $docs_contents);
		return;
	}

	$calling_url = $params->callingUrl;
	$feature_refs = $params->targetFeatureRefs ?? [];

	$tasks = [];
	foreach($feature_refs as $task) {
		$tasks[$task] = 'fail';
	}

	$reduced_calling_url = str_replace(BASE_URL, '', $calling_url);
	$url_segments = explode('/', $reduced_calling_url);
	$last_chapter = $docs_contents->table_of_contents[count($docs_contents->table_of_contents)-1];
	$last_chapter_segment = url_title($last_chapter['dir_label']);

    // Make sure calling URL is formatted in a manner that is expected.
	if (($last_chapter_segment !== $url_segments[0]) || (count($url_segments) !== 3)) {
		http_response_code(400);
		die();
	}

	// Get section and sub directories
	foreach($last_chapter['sub_directories'] as $last_chapter_section) {
		$next_segment = url_title($last_chapter_section['dir_label']);
		if ($next_segment === $url_segments[1]) {
			$target_section = $last_chapter_section;
			$sub_directories = $target_section['sub_directories'];

			foreach($sub_directories as $sub_directory) {
				$files = $sub_directory['files'];

				foreach($files as $file) {
					$filename = $file['filename'] ?? '';
					$filename_str = str_replace('.html', '', $filename);

					if (in_array($filename_str, $feature_refs)) {
						$filepath = $file['filepath'];
						$feature_description = extract_feature_description($filepath);

						if (strlen($feature_description) > 5) {
							if(isset($tasks[$filename_str])) {
								//$feature_description = str_replace('\n     ', '', $feature_description);

								$tasks[$filename_str] = $feature_description;
							}
						}
					}
				}
			}
		}
	}

	echo json_encode($tasks);

	//echo $feature_refs_json;
} else {
	http_response_code(400);
	die();
}

function extract_content($string, $start, $end) {
    $pos = stripos($string, $start);
    $str = substr($string, $pos);
    $str_two = substr($str, strlen($start));
    $second_pos = stripos($str_two, $end);
    $str_three = substr($str_two, 0, $second_pos);
    $content = trim($str_three); // Remove whitespaces
    return $content;
}

function extract_feature_description($filepath) {
	$filepath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filepath);
	if (!file_exists($filepath)) {
		http_response_code(400);
		die();
	}

	$must_have = APPPATH.REF_DIR;
	$must_have = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $must_have);

	if (strpos($filepath, $must_have) === false) {
		http_response_code(400);
		die();
	}

	$file_contents = file_get_contents($filepath);
	$start_str = '<h2>Description</h2>';
	$end_str = '<h2>';
	$description = extract_content($file_contents, $start_str, $end_str);
	$description = nl2br($description);
	$description = strip_tags($description);
	$description = out($description);
	$description = trim($description);
	return $description;
}

function out($input, $encoding = 'UTF-8', $output_format = 'html') {
    $flags = ENT_QUOTES;

    if ($input === null) {
        $input = '';
    }

    if ($output_format === 'xml') {
        $flags = ENT_XML1;
    } elseif ($output_format === 'json') {
        // Customize JSON escaping as needed
        $input = json_encode($input, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $flags = ENT_NOQUOTES;
    } elseif ($output_format === 'javascript') {
        // JavaScript-encode the input
        $input = json_encode($input, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    } elseif ($output_format === 'attribute') {
        // Escape for HTML attributes
        $flags = ENT_QUOTES;
    } else {
        // Dynamically choose the right function
        $input = ($output_format === 'html') ? htmlspecialchars($input, $flags, $encoding) : htmlentities($input, $flags, $encoding);
        return $input;
    }

    return htmlspecialchars($input, $flags, $encoding);
}

function attempt_serve_feature_details($params, $docs_contents) {
	$feature_path = $params->featurePath;
	$path_bits = explode('/', $feature_path);

	if (($path_bits[0] !== REF_DIR) || (count($path_bits) !== 4)) {
		http_response_code(400);
		die();
	}

	$last_chapter = $docs_contents->table_of_contents[count($docs_contents->table_of_contents)-1];
	$last_chapter_sections = $last_chapter['sub_directories'];
	foreach($last_chapter_sections as $last_chapter_section) {
		if ($last_chapter_section['dir_name'] === $path_bits[1]) {

			$section_sub_dirs = $last_chapter_section['sub_directories'];
			foreach($section_sub_dirs as $section_sub_dir) {
				$files = $section_sub_dir['files'];
				foreach($files as $file) {
					$reduce_file_path = str_replace(APPPATH, '', $file['filepath']);
					$last_five = substr($reduce_file_path, -5);

					if (($reduce_file_path === $feature_path) && ($last_five === '.html')) {
						// Match!  Now, safe to fetch and serve contents.
						http_response_code(200);
						$file_content = file_get_contents($file['filepath']);
						echo $file_content;
						die();
					}
				}
	
			}

		}
	}

	http_response_code(400);
	die();

}














