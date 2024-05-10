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

	if (isset($params->targetFeatureRefs)) {
		// Build up and array of target feature details to be fetched
		$target_features = $params->targetFeatureRefs;
		$return_short_descriptions = true;
	} else {
		$return_short_descriptions = false;
		$feature_ref['feature_type'] = $params->feature_type ?? '';
		$feature_ref['feature_ref_dir'] = $params->feature_ref_dir ?? '';
		$feature_ref['name'] = $params->name ?? '';
		$target_features[] = (object) $feature_ref;
	}

	$found_feature_refs = $docs->extract_feature_refs($target_features, $docs_contents, true);
	$results = [];
	foreach($found_feature_refs as $key => $value) {
		$results[$key] = $value['information'] ?? '';

		if ($return_short_descriptions === true) {
			$results[$key] = extract_feature_description($results[$key]);
		}
		
	}
	http_response_code(200);
	echo json_encode($results);
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

function extract_content($string, $start, $end) {
    $pos = stripos($string, $start);
    $str = substr($string, $pos);
    $str_two = substr($str, strlen($start));
    $second_pos = stripos($str_two, $end);
    $str_three = substr($str_two, 0, $second_pos);
    $content = trim($str_three); // Remove whitespaces
    return $content;
}

function extract_feature_description($file_contents) {
	$start_str = '<h2>Description</h2>';
	$end_str = '<h2>';
	$description = extract_content($file_contents, $start_str, $end_str);
	$description = nl2br($description);
	$description = strip_tags($description);
	$description = out($description);
	$description = trim($description);
	return $description;
}