<h1><?= $table_of_contents[count($table_of_contents)-1]['dir_label'] ?></h1>
<p>This resource provides extensive information about all publicly accessible APIs within the Trongate framework, including details pertaining to pre-installed modules.</p>

<p><?php
$this_url = rtrim(current_url(), '/');
foreach($table_of_contents[count($table_of_contents)-1]['sub_directories'] as $ref_section) {
	$target_url = $this_url.'/'.url_title($ref_section['dir_label']);

	echo anchor($target_url, $ref_section['dir_label'], array('class' => 'button api-ref-btn'));
}
?></p>

<p class="mt-3 sm"><strong>PLEASE NOTE:</strong> Details regarding the internal mechanisms of the Trongate framework have been excluded from the API reference guide. Additionally, information about methods within pre-installed modules that are not marked as 'public' has also been omitted. These exclusions are deliberate to ensure clarity and relevance for developers who are learning or working with Trongate.</p>


<div class="alert alert-info">If you notice any errors or inaccuracies, or require assistance understanding any aspect of the Trongate API reference guide, please visit the Trongate <a href="https://trongate.io/forums/display/the-help-bar">Help Bar</a>, an online discussion forum dedicated to supporting developers like you.</div>



