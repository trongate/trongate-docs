<?php
include('Docs.php');
$docs = new Docs;
/*
    SAMPLE DATA:

	{
	  "chapter_title": "Getting Started",
	  "pages": [
	    {
	      "id": 51,
	      "page_title": "A Word About MariaDB And MySQL",
	      "meta_description": "",
	      "meta_keywords": "",
	      "priority": 1,
	      "display": 1,
	      "docs_categories_id": 1,
	      "url_string": "a-word-about-mariadb-and-mysql"
	    },
	    {
	      "id": 46,
	      "page_title": "Installing PHP and MySQL",
	      "meta_description": "",
	      "meta_keywords": "XAMPP,WAMP,MAMP,AMPPS,development,environment",
	      "priority": 2,
	      "display": 1,
	      "docs_categories_id": 1,
	      "url_string": "installing-php-and-mysql"
	    },
	    {
	      "id": 10,
	      "page_title": "Let's Get Trongate!",
	      "meta_description": "",
	      "meta_keywords": "installation, setup, set-up",
	      "priority": 3,
	      "display": 1,
	      "docs_categories_id": 1,
	      "url_string": "lets-get-trongate"
	    }
	  ],
	  "chapterNum": 2
	}

*/

$posted_params = file_get_contents('php://input');
$params = json_decode($posted_params);

// Check if JSON decoding was successful
if ($params !== null) {

    $chapter_num = $params->chapterNum;
    $chapter_num_id = $chapter_num*10;

    if (strlen($chapter_num_id) === 3) {
    	$chapter_start_str = '0';
    } else {
    	$chapter_start_str = '00';
    }

    $chapter_title = $params->chapter_title;
    $safe_chapter_title = url_title($chapter_title);
    $chapter_dir_name = $chapter_start_str.$chapter_num_id.$safe_chapter_title;

    $chapter_path = APPPATH.'trongate-docs/'.$chapter_dir_name;

    echo $chapter_path; die();



    //echo $chapter_dir_name; die();

} else {
	http_response_code(400);
	echo 'Bad request.';
	die();
}

function create_writable_directory($directory_name, $permissions = 0777) {
    // Check if the directory already exists
    if (!file_exists($directory_name)) {
        // Attempt to create the directory with specified permissions
        if (mkdir($directory_name, $permissions, true)) {
            echo "Directory '{$directory_name}' was created successfully.\n";
        } else {
            echo "Failed to create directory.\n";
            return false;  // Return false if the directory could not be created
        }
    } else {
        echo "Directory '{$directory_name}' already exists.\n";
    }

    // Set permissions for the directory
    if (chmod($directory_name, $permissions)) {
        echo "Directory permissions have been set to writable.\n";
        return true;  // Return true if everything was successful
    } else {
        echo "Failed to set directory permissions.\n";
        return false;  // Return false if setting permissions failed
    }
}


function url_title($value, $transliteration = true) {
    if (extension_loaded('intl') && $transliteration === true) {
        $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
        $value = $transliterator->transliterate($value);
    }
    $slug = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    $slug = preg_replace('~[^\pL\d]+~u', '-', $slug);
    $slug = trim($slug, '- ');
    //$slug = strtolower($slug);
    return $slug;
}