<?php

// Comprehensive Reference Guide START

$sub_directories = $chapter['sub_directories'];
foreach($sub_directories as $sub_directory) {
    $dir_label = $sub_directory['dir_label'];
    echo '<li class="ref-sub-headline">'.$dir_label.'</li>';

    echo '<ul>';

    
    foreach($sub_directory['sub_directories'] as $dir) {

        if (isset($dir['files'][0])) {
           
            $page_url = $dir['files'][0]['page_url'];

            // Trim leading and trailing slashes then split the path into segments
            $segments = explode('/', trim($page_url, '/'));

            // Remove the last segment
            array_pop($segments);

            // Reconstruct the URL without the last segment
            $page_url = implode('/', $segments);

        } else {
            $page_url = '#';
        }


        echo '<li><a href="'.$page_url.'">'.$dir['dir_label'].'</a></li>';
    }

    echo '</ul>';

}
?>