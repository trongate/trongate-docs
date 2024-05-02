<h1>Comprehensive Reference Guide</h1>
<p>This chapter provides detailed documentation on the extensive array of classes, methods, and functions available within the Trongate framework. Each entry is thoroughly documented to offer comprehensive insight into the functionalities and utilities provided.</p>

<div class="toc-grid">
<?php
$ref_root = '0300Comprehensive_Reference_Guide';
$last_chapter = $table_of_contents[count($table_of_contents)-1];
foreach($last_chapter['sub_directories'] as $sub_directory) {
	?>

<div class="card">
  <div class="card-body">
    <h2><?= $sub_directory['dir_label'] ?></h2>

    <ul>
    	<?php
    	$root_page_path = BASE_URL.$ref_root.'/'.$sub_directory['dir_name'];
        foreach($sub_directory['sub_directories'] as $page) {
        	$ref_page_url = $root_page_path.'/'.$page['dir_name'];
        	echo '<li><a href="'.$ref_page_url.'">'.$page['dir_label'].'</a></li>';
        }
    	?>
    </ul>
  </div>
</div>

<?php
}
?>
</div>