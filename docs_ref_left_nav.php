<?php
$sub_directories = $chapter['sub_directories'];
$ref_chapter_root = $this->remove_first_four_if_numeric(url_title(REF_DIR));
foreach($sub_directories as $sub_directory) {
    echo '<li>'.anchor($ref_chapter_root.'/'.url_title($sub_directory['dir_name']), $sub_directory['dir_label']).'</li>';
}
