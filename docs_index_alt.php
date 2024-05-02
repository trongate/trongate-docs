<h1><?= $page_headline ?></h1>

<ul>
<?php
foreach($html_files as $html_file) {
	echo '<li class="fake-link" onclick="initOpenInfo(\''.$html_file['page_url'].'\')">'.$html_file['page_label'].'</li>';
}
?></ul>