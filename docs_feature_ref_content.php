<?php
$this_url = rtrim(current_url(), '/');
$last_chapter = $table_of_contents[count($table_of_contents)-1];
$intro_text = ''; // fallback

if (strpos($this_url, 'helper')) {
    $entity_type = 'helper';
    $entity_type_plural = 'helpers';
    $item_type = 'function';
} else {
    $entity_type = 'class';
    $entity_type_plural = 'classes';
    $item_type = 'method';
}

$last_chapter_sections = $last_chapter['sub_directories'];
foreach($last_chapter_sections as $last_chapter_section) {
	$next_segment = url_title($last_chapter_section['dir_label']);
	if ($next_segment === segment(2)) {
		$headline_text = $last_chapter_section['dir_label'];

		// Build a list of links
		$section_links = [];
		$section_sub_dirs = $last_chapter_section['sub_directories'];
		foreach($section_sub_dirs as $section_sub_dir) {
			$row_data['label']= $section_sub_dir['dir_label'];
			$row_data['page_url'] = $this_url.'/'.url_title($section_sub_dir['dir_label']);
			$section_links[] = $row_data;

			$section_sub_dir_segment = url_title($section_sub_dir['dir_label']);
			if ($section_sub_dir_segment === segment(3)) {
				$row_data['label'] = $section_sub_dir['dir_label'];
				$row_data['page_url'].= '/'.segment(3);
				$breadcrumbs_array[] = $row_data;
				break;
			}
		}

		break;
	}
}

if (segment(3) === '') {
	$intro_text = 'Below is a list of '.$entity_type_plural.' within the Trongate framework that are accessible to developers:';
} else {
	$headline_text = $section_sub_dir['dir_label'];
	$features_items = [];
	foreach($section_sub_dir['files'] as $target_file) {
		$page_url_segments = explode('/', $target_file['page_url']);
		$feature_name = $page_url_segments[count($page_url_segments)-1];
		$features_items[] = str_replace('.html', '', $feature_name);
	}

	unset($section_links);
}
?>
<h1><?= $headline_text ?></h1>
<p><?= $intro_text ?></p>
<ol id="feature-ref-list" class="mt-2">
	<?php
	if (isset($section_links)) {
		foreach($section_links as $section_link) {
			echo '<li><b>'.anchor($section_link['page_url'], $section_link['label']).'</span></b>';
		}
	}

	if (isset($features_items)) {
		foreach($features_items as $feature_item) {
			echo '<li><span class="feature-ref">'.$feature_item.'</span></li>';
		}		
	}
	?>
</ol>

