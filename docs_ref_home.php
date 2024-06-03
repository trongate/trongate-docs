<h1><?= $table_of_contents[count($table_of_contents)-1]['dir_label'] ?></h1>
<p>This resource provides comprehensive information regarding all of the publicly accessible APIs within the Trongate framework. It does not, however, include details on the internal mechanisms of the framework, which are beyond the intended scope of this documentation.</p>

<p><?php
$this_url = rtrim(current_url(), '/');
foreach($table_of_contents[count($table_of_contents)-1]['sub_directories'] as $ref_section) {
	$target_url = $this_url.'/'.url_title($ref_section['dir_label']);

	echo anchor($target_url, $ref_section['dir_label'], array('class' => 'button api-ref-btn'));
}
?></p>
