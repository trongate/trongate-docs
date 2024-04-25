<?php
include('Docs.php');
$docs = new Docs;

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
    $safe_chapter_title = str_replace('-', '_', $safe_chapter_title);
    $chapter_dir_name = $chapter_start_str.$chapter_num_id.$safe_chapter_title;
    $chapter_path = APPPATH.'trongate-docs/'.$chapter_dir_name;

    $result = create_writable_directory($chapter_path);
     
    if ($result === true) {

    	$pages = $params->pages;
    	build_chapter_pages($pages, $chapter_path);
    	echo 'great success';
    }

} else {
	http_response_code(400);
	echo 'Bad request.';
	die();
}

function extract_and_format_code($html) {
    // Define the pattern to match [code]...[/code] blocks
    $pattern = '/\[code\](.*?)\[\/code\]/s';

    // Use preg_replace_callback to process each match
    $formatted_html = preg_replace_callback($pattern, function ($matches) {
        // Extract the content between [code] tags
        $code_content = $matches[1];

        // Convert <br> to new lines and remove HTML tags that might be inside the code block
        $code_content = strip_tags($code_content, '<br>'); // Allow <br> temporarily
        $code_content = str_replace('<br>', "\n", $code_content);


        $ditch = '[php-open-short]';
        $replace = '<?=';
        $code_content = str_replace($ditch, $replace, $code_content);

        $ditch = '?&gt;';
        $replace = '?>';
        $code_content = str_replace($ditch, $replace, $code_content);

        $ditch = '&amp;lt;';
        $replace = '&lt;';
        $code_content = str_replace($ditch, $replace, $code_content);

        $ditch = '&lt;';
        $replace = '<';
        $code_content = str_replace($ditch, $replace, $code_content);

        $ditch = '&amp;gt;';
        $replace = '&gt;';
        $code_content = str_replace($ditch, $replace, $code_content);

        $ditch = '&gt;';
        $replace = '>';
        $code_content = str_replace($ditch, $replace, $code_content);


        $code_block = "<pre><code>" . htmlspecialchars($code_content) . "</code></pre>";

        $ditch = '&#039;';
        $replace = '"';
        $code_block = str_replace($ditch, $replace, $code_block);

        $ditch = '&quot;';
        $replace = '"';
        $code_block = str_replace($ditch, $replace, $code_block);

        $ditch = '[php-open-long]';
        $replace = '&lt;?php';
        $code_block = str_replace($ditch, $replace, $code_block);

        $ditch = '<em> </em>';
        $replace = '';
        $code_block = str_replace($ditch, $replace, $code_block);

        $ditch = '<h2><strong>';
        $replace = '<h2>';
        $code_block = str_replace($ditch, $replace, $code_block);

        $ditch = '<h2><strong>';
        $replace = '<h2>';
        $code_block = str_replace($ditch, $replace, $code_block);

        $ditch = '</strong></h2>';
        $replace = '</h2>';
        $code_block = str_replace($ditch, $replace, $code_block);

    




        // Return the formatted code block
        //return "<pre><code>" . htmlspecialchars($code_content) . "</code></pre>";
        return $code_block;
    }, $html);

    return $formatted_html;
}

function create_html_file($page_filename, $page_content, $dir) {
    // Build the full path for the new file
    $file_path = $dir . '/' . $page_filename;

    // Ensure the directory exists
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);  // Create the directory if it does not exist
    }

    $page_content = clean_page_content($page_content);

    // Attempt to write the content to the file
    if (file_put_contents($file_path, $page_content) !== false) {
        // Set the file permission to 777
        chmod($file_path, 0777);

        return true; // Return true if file was created and ownership was possibly set
    } else {
        echo "Failed to create file.";
    }

    return false; // Return false if the creation failed
}

    function clean_page_content($page_content) {

        $ditch_array = [];

        $row_data['ditch'] = '&nbsp; ';
        $row_data['replace'] = ' ';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '<p><br></p>';
        $row_data['replace'] = '';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '&nbsp;</p>';
        $row_data['replace'] = '</p>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = ' </p>';
        $row_data['replace'] = '</p>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = 'â€‹';
        $row_data['replace'] = '';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '‹â€';
        $row_data['replace'] = '';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = 'â€';
        $row_data['replace'] = '';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '&nbsp;</p>';
        $row_data['replace'] = '</p>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '&nbsp;</p>';
        $row_data['replace'] = '</p>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '&nbsp;<br></p>';
        $row_data['replace'] = '</p>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '<br></p>';
        $row_data['replace'] = '</p>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '<br></li>';
        $row_data['replace'] = '</li>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = ' </li>';
        $row_data['replace'] = '</li>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '&nbsp;';
        $row_data['replace'] = ' ';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '™';
        $row_data['replace'] = '\'';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '˜';
        $row_data['replace'] = '\'';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = ' <em>';
        $row_data['replace'] = '<em>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '“';
        $row_data['replace'] = '-';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '[alert-info]';
        $row_data['replace'] = '[alert alert-info]';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '[alert-warning]';
        $row_data['replace'] = '[alert alert-warning]';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '[alert-success]';
        $row_data['replace'] = '[alert alert-success]';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '[alert-danger]';
        $row_data['replace'] = '[alert alert-danger]';
        $ditch_array[] = $row_data;


        $row_data['ditch'] = '<p>[alert';
        $row_data['replace'] = '[alert';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '<p>[/alert]</p>';
        $row_data['replace'] = '[/alert]';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '[alert]';
        $row_data['replace'] = '<div class="alert">';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '[alert alert-info]';
        $row_data['replace'] = '<div class="alert alert-info">';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '[alert alert-warning]';
        $row_data['replace'] = '<div class="alert alert-warning">';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '[alert alert-success]';
        $row_data['replace'] = '<div class="alert alert-success">';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '[alert alert-danger]';
        $row_data['replace'] = '<div class="alert alert-danger">';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '[/alert]';
        $row_data['replace'] = '</div>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '<h2><br></h2>';
        $row_data['replace'] = '';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '<em>';
        $row_data['replace'] = ' <em>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '  <em>';
        $row_data['replace'] = ' <em>';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '<p></p>';
        $row_data['replace'] = '';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = '<p><strong><br></strong></p>';
        $row_data['replace'] = '';
        $ditch_array[] = $row_data;
        
        foreach($ditch_array as $dp) {
            $page_content = str_replace($dp['ditch'], $dp['replace'], $page_content);
        }

        $page_content = convert_custom_links_to_html($page_content);
        $page_content = beautify_html($page_content);

        $page_content = extract_and_format_code($page_content);

        $page_content = convert_image_blocks_to_img_tags($page_content);

        return $page_content;
    }

function convert_image_blocks_to_img_tags($html) {
    return preg_replace_callback(
        '/\[image src="([^"]+)"\](.*?)\[\/image\]/',
        function ($matches) {
            $src = $matches[1];

            $src = str_replace('-png', '.png', $src);
            $src = str_replace('-jpg', '.jpg', $src);
            $src = str_replace('-jpeg', '.jpeg', $src);
            $src = str_replace('-gif', '.gif', $src);
            $src = str_replace('https://trongate.io/docs_pages_pictures/', '../images/', $src);

            $alt = $matches[2];
            return '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($alt) . '">';
        },
        $html
    );
}


function beautify_html($html_string) {
    // Initialize a cURL session
    $curl = curl_init();

    // Set the URL to the external service
    $url = 'https://www.freeformatter.com/html-formatter.html';

    // Set up the data you want to send
    $data = [
        'htmlString' => $html_string,
        'forceInNewWindow' => 'true',
        'encoding' => 'UTF-8',  // Specify the encoding
        'indentation' => 'FOUR_SPACES'  // Specify the indentation level
    ];

    // Convert the $data array to a URL-encoded query string
    $postData = http_build_query($data);

    // Set options for the cURL session
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // Return the transfer as a string
    curl_setopt($curl, CURLOPT_HEADER, false);  // Do not include the header in the output
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification if necessary

    // Execute the cURL session
    $result = curl_exec($curl);

    // Check for errors
    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        curl_close($curl);
        return "cURL error: " . $error_msg;
    }

    // Close the cURL session
    curl_close($curl);

    // Return the result
    return $result;
}




function convert_custom_links_to_html($text) {
    // Define the pattern to search for
    $pattern = '/\[link="([^"]+)"\](.*?)\[\/link\]/';

    // Define the replacement pattern
    $replacement = '<a href="$1">$2</a>';

    // Replace the custom markup with standard HTML link tags
    $converted_text = preg_replace($pattern, $replacement, $text);

    return $converted_text;
}


function build_chapter_pages($pages, $dir) {
	
    $page_counter = 0;
	foreach($pages as $page) {
        $page_counter = $page_counter+10;
        $str_start = str_pad($page_counter, 4, '0', STR_PAD_LEFT);

		// Establish what the file name should be for this page.
            $page_title = $page->page_title;
            $page_title = correct_page_title($page_title);
		    $page_title = strtolower(url_title($page_title));
		    $page_title = str_replace('-', '_', $page_title);
		    $page_filename = $str_start.$page_title.'.html';
		    $page_content = $page->document_body;


	        create_html_file($page_filename, $page_content, $dir);

	}


}

function create_writable_directory($directory_name, $permissions = 0777) {
    // Check if the directory already exists
    if (!file_exists($directory_name)) {
        // Attempt to create the directory with specified permissions
        if (mkdir($directory_name, $permissions, true)) {
            return true;
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

function  correct_page_title($page_title) {

        $ditch_array = [];

        $row_data['ditch'] = 'A Few Words About';
        $row_data['replace'] = 'Regarding';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = 'A Word About';
        $row_data['replace'] = 'Regarding';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = 'The Structure of a Trongate Web App';
        $row_data['replace'] = 'Trongate App Structure';
        $ditch_array[] = $row_data;

        $row_data['ditch'] = 'Isn\'t this the same as HMVC?';
        $row_data['replace'] = 'HAVC vs HMVC';
        $ditch_array[] = $row_data;


        foreach($ditch_array as $dp) {
            $page_title = str_replace($dp['ditch'], $dp['replace'], $page_title);
        }

        $page_title = convert_custom_links_to_html($page_title);
        $page_title = beautify_html($page_title);

        return $page_title;
}