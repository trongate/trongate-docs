<?php
class Docs {

	private $view_files = [];
	private $feature_refs = [];

	public function get_next_prev_array($docs_contents) {

		$next_prev_array['prev'] = false;
		$next_prev_array['next'] = false;

		$assumed_page_url = rtrim(current_url(), '/');
		$reduced_base_url = rtrim(BASE_URL, '/');

		$exclude_these_segments[] = '';
		$exclude_these_segments[] = 'introduction';
		$exclude_these_segments[] = $this->remove_first_four_if_numeric(url_title(REF_DIR));

		if (in_array(segment(1), $exclude_these_segments)) {
			return $next_prev_array;
		} else {

			$prev_url = $this->attempt_extract_prev_url($assumed_page_url, $docs_contents->table_of_contents);
			$next_url = $this->attempt_extract_next_url($assumed_page_url, $docs_contents->table_of_contents);

			if ($prev_url !== '') {
				$next_prev_array['prev'] = $prev_url;
			}

			if ($next_url !== '') {
				$next_prev_array['next'] = $next_url;
			}

			return $next_prev_array;

		}

	}

	private function attempt_extract_next_url($assumed_page_url, $table_of_contents) {

		$found_assumed_page_url = false;
		$next_url = '';
		foreach($table_of_contents as $chapter) {
			$chapter_pages = $chapter['files'];
			foreach($chapter_pages as $chapter_page) {

				if ($found_assumed_page_url === true) {
					$next_url = $chapter_page['page_url'];
					return $next_url;
				}

				if ($chapter_page['page_url'] === $assumed_page_url) {
					$found_assumed_page_url = true;
				}
				
			}

		}

		return $next_url;

	}

	function remove_last_segment() {
	    // Get the current URL
	    $current_url = current_url();

	    // Remove the BASE_URL part to isolate the path
	    $path = str_replace(BASE_URL, '', $current_url);

	    // Trim leading and trailing slashes then split the path into segments
	    $segments = explode('/', trim($path, '/'));

	    // Remove the last segment
	    array_pop($segments);

	    // Reconstruct the URL without the last segment
	    return BASE_URL . implode('/', $segments);
	}


	private function attempt_extract_prev_url($assumed_page_url, $table_of_contents) {

		$prev_url = '';
		foreach($table_of_contents as $chapter) {
			$chapter_pages = $chapter['files'];
			$page_counter = 0;
			foreach($chapter_pages as $chapter_page) {
				$page_counter++;
				if ($chapter_page['page_url'] === $assumed_page_url) {
					return $prev_url;
					break;
				}

				$prev_url = $chapter_page['page_url'];
			}

		}

	}

	public function est_docs_contents() {
		// Scan all of the directories and return an array that contains:
		// 1).  A table of contents array and 2).  An array of feature_refs.

		// Establish an array of directories to be checked.
		$reduced_apppath = rtrim(APPPATH, '/');
		$docs_contents_array['table_of_contents'] = $this->get_sub_directories($reduced_apppath, EXCLUDE_DIRS);
		$docs_contents_array['view_files'] = $this->view_files;
		$docs_contents_array['feature_refs'] = $this->feature_refs;
		$docs_contents_array['homepage'] = $this->get_homepage($docs_contents_array['table_of_contents']);
	
		$docs_contents = (object) $docs_contents_array;
		return $docs_contents;
	}

	function return_view_file_path($docs_contents) {
		$view_files = $docs_contents->view_files;
		$view_file_path = '';

		$assumed_page_url = rtrim(current_url(), '/');
		$reduced_base_url = rtrim(BASE_URL, '/');

	    if ($assumed_page_url === $docs_contents->homepage['page_url']) {
	    	header("location: ".BASE_URL);
	    	die();
	    }

		if ($assumed_page_url === $reduced_base_url) {
			$view_file_path = $docs_contents->homepage['filepath'];
		} else {

			foreach($view_files as $view_file) {
				if ($view_file['page_url'] === $assumed_page_url) {
					$view_file_path = $view_file['view_file_path'];
					break;
				}
	
			}

		}

		if ((segment(1) !== '') && (segment(2) === '')) {
			$view_file_path = APPPATH.'docs_chapter_intro.php';
		}

		return $view_file_path;
	}

	public function get_chapter_intro_data($docs_contents) {

		$chapter_intro_data['chapter_num'] = 0;
		$chapter_intro_data['chapter_name'] = '';
		$chapter_intro_data['first_page'] = '';
		$chapter_intro_data['prev_page'] = '';

		$table_of_contents = $docs_contents->table_of_contents;

		$chapter_counter = 0;
		foreach($table_of_contents as $chapter) {

			$chapter_intro_data['chapter_num']++;

			$chapter_pages = $chapter['files'];
			foreach($chapter_pages as $chapter_page) {

				if (strpos($chapter_page['page_url'], current_url()) !== false) {
					$chapter_intro_data['chapter_name'] = $chapter['dir_label'];
					$chapter_intro_data['first_page'] = $chapter['files'][0]['page_url'];

					if ($chapter_intro_data['chapter_num'] > 1) {
						$prev_chapter = $table_of_contents[$chapter_intro_data['chapter_num']-2];
						$prev_chapter_pages = $prev_chapter['files'];
						$chapter_intro_data['prev_page'] = $prev_chapter_pages[count($prev_chapter_pages)-1]['page_url'];
					}

					return $chapter_intro_data;
					break;
				}

			}

		}

		return $chapter_intro_data;

	}

	public function get_homepage($table_of_contents) {
		$first_page = $table_of_contents[0]['files'][0];
		return $first_page;
	}

	private function get_sub_directories($path, $exclude_dirs = []) {

	    $sub_directories = [];
	    if (!is_dir($path)) {
	        throw new InvalidArgumentException("Error: The provided path is not a directory.");
	    }

	    $dir_handle = opendir($path);
	    if (!$dir_handle) {
	        throw new RuntimeException("Error: Unable to open directory.");
	    }

	    while (false !== ($entry = readdir($dir_handle))) {
	        if ($this->should_exclude($entry, $exclude_dirs)) {
	            continue;
	        }

	        $full_path = $path . DIRECTORY_SEPARATOR . $entry;
	        if (is_dir($full_path)) {
	            $label = $this->format_directory_name($entry);
	            $sub_sub_dirs = $this->get_sub_directories($full_path);
	            $files = $this->fetch_html_files($full_path);

	            $sub_directories[] = [
	                'dir_name' => $entry,
	                'dir_label' => ucwords($label),
	                'dir_path' => $full_path,  // Adding the full path of the directory
	                'sub_directories' => $sub_sub_dirs,
	                'files' => $files
	            ];
	        }
	    }

	    closedir($dir_handle);
	    usort($sub_directories, function($a, $b) {
	        return strcmp($a['dir_name'], $b['dir_name']);
	    });

	    return $sub_directories;
	}

	private function should_exclude($entry, $exclude_dirs) {
	    return $entry[0] === '.' || in_array($entry, $exclude_dirs);
	}

	private function format_directory_name($name) {
	    $name = preg_replace('/^\d+/', '', $name);
	    return trim(str_replace('_', ' ', $name));
	}

	private function fetch_html_files($directory_path) {

	    $html_files = [];
	    if (!is_dir($directory_path)) {
	        throw new InvalidArgumentException("Error: The provided path is not a directory.");
	    }

	    $dir_handle = opendir($directory_path);
	    if (!$dir_handle) {
	        throw new RuntimeException("Error: Unable to open directory.");
	    }

	    while (false !== ($file = readdir($dir_handle))) {
	        if ($file[0] === '.' || pathinfo($file, PATHINFO_EXTENSION) !== 'html') {
	            continue;
	        }

	        $full_path = $directory_path . DIRECTORY_SEPARATOR . $file;
	        if (!is_file($full_path)) {
	            continue;
	        }

	        $label = ucwords(str_replace('_', ' ', basename($file, '.html')));
	        $label = $this->build_nice_label($label);
	        $file_relative_path = str_replace(APPPATH, '', $full_path);
	        $file_relative_path = str_replace('\\', '/', $file_relative_path); // Ensure forward slashes
	        $file_url_path = str_replace('_', '-', $file_relative_path); // Replace underscores with hyphens
	        $file_url_path = $this->remove_first_four_if_numeric($file_url_path);

	        $page_url = BASE_URL . ltrim($file_url_path, '/');
	        $page_url = $this->format_page_url($page_url);

	        $row_data['view_file_path'] = $full_path;
	        $row_data['page_url'] = $page_url;

	        $this->view_files[] = $row_data;

	        if (strpos($full_path, REF_DIR)) {

	        	$full_path_bits = explode('/', $full_path);
	        	$last_path_segment = $full_path_bits[count($full_path_bits)-1];
	        	$last_path_segment = str_replace('.html', '', $last_path_segment);

	        	if (!isset($this->feature_refs[$last_path_segment])) {
	        		$this->feature_refs[$last_path_segment] = $row_data;
	        	}

	        }

	        $html_files[] = [
	            'filename' => $file,
	            'filepath' => $full_path,
	            'label' => $label,
	            'page_url' => $page_url
	        ];

	    }

	    closedir($dir_handle);
	    return $html_files;
	}

	function format_page_url($page_url) {
		$temp_url = str_replace(BASE_URL, '', $page_url);
		$temp_url = str_replace('.html', '', $temp_url);

		$url_segments = explode('/', $temp_url);
		$new_page_url = BASE_URL;

		foreach ($url_segments as $url_segment) {
			$url_segment = url_title($url_segment);
			$new_page_url.= $this->remove_first_four_if_numeric($url_segment).'/';
		}

		$new_page_url = strtolower($new_page_url);
		$new_page_url = rtrim($new_page_url, '/');
		$new_page_url.= '.html';
		return $new_page_url;
	}

    private function build_nice_label($str) {
    	$page_label = $this->remove_first_four_if_numeric($str);
    	$page_label = str_replace('_', ' ', $page_label);
    	$page_label = ucwords($page_label);
    	$page_label = str_replace('.html', '', $page_label);
 		$page_label = str_replace('rongate1', 'rongate #1', $page_label);
        $page_label = str_replace('rongate2', 'rongate #2', $page_label);
        $page_label = str_replace('rongate3', 'rongate #3', $page_label);
        $page_label = str_replace('rongate4', 'rongate #4', $page_label);
        $page_label = str_replace('rongate5', 'rongate #5', $page_label);
        $page_label = str_replace('Github', 'GitHub', $page_label);
        $page_label = str_replace('And', '&amp;', $page_label);
        $page_label = str_replace('Url ', 'URL ', $page_label);
        $page_label = str_replace('Css', 'CSS', $page_label);
        $page_label = str_replace('themes', 'Themes', $page_label);
        $page_label = str_replace(' An Overview', ': An Overview', $page_label);
        $page_label = str_replace(' Of ', ' of ', $page_label);
        $page_label = str_replace(' The ', ' the ', $page_label);
        $page_label = str_replace(' A ', ' a ', $page_label);
        $page_label = str_replace(' From ', ' from ', $page_label);
    	return $page_label;
    }


	private function remove_first_four_if_numeric($string) {
	    // Extract the first four characters
	    $first_four = substr($string, 0, 4);

	    // Check if these characters are numeric
	    if (ctype_digit($first_four)) {
	        // Remove the first four characters and return the rest of the string
	        return substr($string, 4);
	    }

	    // Return the original string if the first four characters are not numeric
	    return $string;
	}

    /**
     * Renders a view and returns the output as a string, or to the browser.
     *
     * @param  string     $view The name of the view file to render.
     * @param  array      $data An array of data to pass to the view file.
     * @param  bool|null  $return_as_str If set to true, the output is returned as a string, otherwise to the browser.
     *
     * @return string|null If $return_as_str is true, returns the output as a string, otherwise returns null.
     * @throws \Exception
     * 
     * @see https://trongate.io/docs/information/understanding-view-files
     */
    public function view(string $view, array $data = [], ?bool $return_as_str = null): ?string {
        $return_as_str = $return_as_str ?? false;
        
        $view_path = $view.'.php';
        extract($data);

        if ($return_as_str) {
            // Output as string
            ob_start();
            require $view_path;
            return ob_get_clean();
        } else {
            // Output view file
            require $view_path;
            return null;
        }
    }



}