<?php
$this_url = rtrim(current_url(), '/');
$last_chapter = $table_of_contents[count($table_of_contents)-1];
$intro_text = ''; // fallback

if (strpos($this_url, 'helper')) {
    $entity_type = 'helper';
    $entity_type_plural = 'helpers';
    $item_type = 'function';
} elseif (strpos($this_url, 'modules')) {
    $entity_type = 'module';
    $entity_type_plural = 'pre-installed modules';
    $item_type = 'method';
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
	$intro_text = 'Below is a list of '.$entity_type_plural.' within the Trongate framework that are available for use:';
	if ($entity_type === 'module') {
		$intro_text = 'Below is a list of pre-installed modules included with the Trongate framework.';
	}

	if ($entity_type_plural === 'helpers') {
		$intro_text = 'Helpers in the Trongate framework are standalone functions designed to assist with common tasks across your applications. Unlike methods within classes, helper functions can be invoked directly, without the need to instantiate a class. This makes them both lightweight and easily accessible from anywhere within your project.  '.$intro_text;
	}

} else {
	$headline_text = $section_sub_dir['dir_label'];
	$features_items = [];

	if (isset($section_sub_dir['files'][0])) {
		$class_view_url = $section_sub_dir['files'][0]['page_url'] ?? '';

		// Establish the name of the class where the feature ref exists.
		$class_url_segments = explode('/', $class_view_url);

		$second_last_segment = strtolower($class_url_segments[count($class_url_segments)-2]);
		$last_six_chars = substr($second_last_segment, -6);

		if ($last_six_chars === '_class') {
			$second_last_segment = str_replace('_class', '', $second_last_segment);
			$target_segment_bits = explode('_', $second_last_segment);
			$target_class_name = $target_segment_bits[count($target_segment_bits)-1];
		} elseif (segment(2) === 'pre-installed-modules') {
			$target_class_name = segment(3);
			$target_class_name = str_replace('-', '_', $target_class_name);
		}

	}

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
<ul id="feature-ref-list" class="mt-2">
	<?php
	if (isset($section_links)) {
		foreach($section_links as $section_link) {
			echo '<li><b>'.anchor($section_link['page_url'], $section_link['label']).'</span></b>';
		}
	}

	if (isset($features_items)) {
		//$target_class_name = 'trongate-pages';
		$additional_code = (isset($target_class_name)) ? ' data-class="'.$target_class_name.'"' : '';
		foreach($features_items as $feature_item) {
			echo '<li class="feature-item" id="feature-li-'.$feature_item.'"><span class="feature-ref"'.$additional_code.'>'.$feature_item.'()</span>';
			echo '<div class="sm"><span class="blink">* fetching description *</span></div>';
			echo '</li>';
		}
	}
	?>
</ul>

<?php
if (segment(3) === 'the-api-class') {
	$additional_info = 'Trongate\'s API class also contains the following public methods:';
	$additional_info.= '<ol>
  <li>batch()</li>
  <li>count()</li>
  <li>create()</li>
  <li>delete()</li>
  <li>destroy()</li>
  <li>exists()</li>
  <li>explorer()</li>
  <li>get()</li>
  <li>insert()</li>
  <li>search()</li>
  <li>update()</li>
  <li>validate_token()</li>
</ol>';
    $additional_info.= '<p>All of these methods are intended for use by Trongate\'s API Manager to handle HTTP requests made to predefined API endpoints. As they are integral components of the framework, customization or modification of these methods is not recommended. Consequently, detailed descriptions of these methods have been omitted from the documentation.';

}

if (isset($additional_info)) {
	echo '<div class="alert alert-info">'.$additional_info.'</div>';
}
?>


<?php
if (isset($features_items)) {
?>
<script>
window.addEventListener("load", function() {
    fetchFeatureItemsInfo();
});
</script>
<?php
}
?>
