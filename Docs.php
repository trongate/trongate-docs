<?php
class Docs {

	private $view_files = [];
	private $feature_refs = [];

	private function build_breadcrumbs_array_ref($docs_contents, $reduced_ref_dir) {
		// Build a breadcrumbs array for the last chapter (ref guide)
		$breadcrumbs_array = [];

		$row_data['label'] = 'Home';
		$row_data['page_url'] = BASE_URL;
		$breadcrumbs_array[] = $row_data;

		$table_of_contents = $docs_contents->table_of_contents;
		$last_chapter = $table_of_contents[count($table_of_contents)-1];

		$row_data['label'] = $last_chapter['dir_label'];
		$row_data['page_url'] = BASE_URL.$reduced_ref_dir;
		$breadcrumbs_array[] = $row_data;

		if (segment(2) !== '') {
			$last_chapter_sections = $last_chapter['sub_directories'];
			foreach($last_chapter_sections as $last_chapter_section) {
				$next_segment = url_title($last_chapter_section['dir_label']);
				if ($next_segment === segment(2)) {
					$row_data['label'] = $last_chapter_section['dir_label'];
					$row_data['page_url'].= '/'.segment(2);
					$breadcrumbs_array[] = $row_data;
					break;
				}
			}
		}

		if ((segment(3) !== '') && ($last_chapter_section)) {

			$section_sub_dirs = $last_chapter_section['sub_directories'];
			foreach($section_sub_dirs as $section_sub_dir) {
				$section_sub_dir_segment = url_title($section_sub_dir['dir_label']);
				if ($section_sub_dir_segment === segment(3)) {
					$row_data['label'] = $section_sub_dir['dir_label'];
					$row_data['page_url'].= '/'.segment(3);
					$breadcrumbs_array[] = $row_data;
					break;
				}

			}

		}

		return $breadcrumbs_array;
	}

	public function build_breadcrumbs_array($docs_contents) {

		$reduced_ref_dir = $this->remove_first_four_if_numeric(url_title(REF_DIR));
		if ($reduced_ref_dir === segment(1)) {
			$breadcrumbs_array = $this->build_breadcrumbs_array_ref($docs_contents, $reduced_ref_dir);
			return $breadcrumbs_array;
		}

		$current_page_label = 'Current Page';

		$row_data['label'] = 'Home';
		$row_data['page_url'] = BASE_URL;
		$breadcrumbs_array[] = $row_data;

		$view_files = $docs_contents->view_files;
		$table_of_contents = $docs_contents->table_of_contents;

		$assumed_page_url = rtrim(current_url(), '/');
		// Is the assumed page_url inside the known view_files?
		foreach($view_files as $view_key => $value) {
			if ($value['page_url'] === $assumed_page_url) {
				$matched_url_obj = (object) $value;
				break;
			}
		}

		if (!isset($matched_url_obj)) {
			// Page not found within view files array
			return [];
		}

		// We must be looking at a page that is inside a chapter of the docs!
		$view_file_path = $matched_url_obj->view_file_path;
		$chapter_dir_path = $this->remove_last_segment($view_file_path);

		foreach($table_of_contents as $chapter_key => $chapter_value) {
			$dir_path = $chapter_value['dir_path'];

			if ($dir_path === $chapter_dir_path) {
				// Found containing chapter!

				if ($chapter_key === 0) {
					return []; // No breadcrumbs on the first (intro) chapter!
				}

				$row_data['label'] = $chapter_value['dir_label'];
				$row_data['page_url'] = $this->remove_last_segment($assumed_page_url);
				$breadcrumbs_array[] = $row_data;

				$chapter_pages = $chapter_value['files'];
				foreach($chapter_pages as $chapter_page) {
					if ($chapter_page['page_url'] === $assumed_page_url) {
						$current_page_label = $chapter_page['label'];
					}
				}

				$row_data['label'] = $current_page_label;
				$row_data['page_url'] = '';
				$breadcrumbs_array[] = $row_data;
			}

		}

		if (count($breadcrumbs_array) === 1) {
			return [];
		} else {
			return $breadcrumbs_array;
		}
		
	}

	public function est_docs_contents() {
		// Scan all of the directories and return an array that contains:
		// table_of_contents, view_files, feature_refs, homepage (info)

		// Establish an array of directories to be checked.
		$reduced_apppath = rtrim(APPPATH, '/');
		$docs_contents_array['table_of_contents'] = $this->get_sub_directories($reduced_apppath, EXCLUDE_DIRS);
		$docs_contents_array['view_files'] = $this->view_files;
		$docs_contents_array['feature_refs'] = $this->feature_refs;
		$docs_contents_array['homepage'] = $this->get_homepage($docs_contents_array['table_of_contents']);
	
		$docs_contents = (object) $docs_contents_array;
		return $docs_contents;
	}

	public function get_next_prev_array($docs_contents) {

			$next_prev_array['prev'] = false;
			$next_prev_array['next'] = false;

			$assumed_page_url = rtrim(current_url(), '/');
			$reduced_base_url = rtrim(BASE_URL, '/');
			// Do not add next prev for first page or table of contents

			if (($assumed_page_url === $docs_contents->homepage['page_url']) || ($assumed_page_url === $reduced_base_url)) {
				return $next_prev_array;
			}

			if (strpos($assumed_page_url, 'table-of-contents') !== false) {
				return $next_prev_array;
			}

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

	private function attempt_extract_prev_url($assumed_page_url, $table_of_contents) {

		$prev_url = '';
		foreach($table_of_contents as $chapter_key => $chapter) {
			$chapter_pages = $chapter['files'];
			foreach($chapter_pages as $page_key => $chapter_page) {

				if ($chapter_page['page_url'] === $assumed_page_url) {

					if ($page_key === 0) {
						
						// Go to the chapter intro page
						$next_url = $this->remove_last_segment($assumed_page_url);
						return $next_url;

					} elseif(isset($table_of_contents[$chapter_key]['files'][$page_key-1])) {
						
						// Go back one page (within this chapter)
						$next_url = $table_of_contents[$chapter_key]['files'][$page_key-1]['page_url'];
						return $next_url;

					}

				}

			}

		}

		return $prev_url;

	}

	private function attempt_extract_next_url($assumed_page_url, $table_of_contents) {
		$next_url = '';
		foreach($table_of_contents as $chapter_key => $chapter) {
			$chapter_pages = $chapter['files'];
			foreach($chapter_pages as $page_key => $chapter_page) {

				if ($chapter_page['page_url'] === $assumed_page_url) {
					if (isset($table_of_contents[$chapter_key]['files'][$page_key+1])) {
						$next_url = $table_of_contents[$chapter_key]['files'][$page_key+1]['page_url'];
						return $next_url;
					} elseif (isset($table_of_contents[$chapter_key+1]['files'][0])) {

						$next_url = $table_of_contents[$chapter_key+1]['files'][0]['page_url']; 
						$next_url = $this->remove_last_segment($next_url);
						return $next_url;			
					}

					if ($next_url === '') {
						$reduced_ref_dir = $this->remove_first_four_if_numeric(url_title(REF_DIR));
						$next_url = BASE_URL.$reduced_ref_dir;
				    }					 

				}

			}

		}

		return $next_url;

	}

	function remove_last_segment($str) {

		if (strpos($str, BASE_URL) !== false) {
			$requires_base_url = true;
			// Remove the BASE_URL part to isolate the path
			$path = str_replace(BASE_URL, '', $str);

		} else {
			$requires_base_url = false;
			$first_character = substr($str, 0, 1);
			$str_start = ($first_character === '/') ? '/' : '';
			$path = $str;
		}

	    // Trim leading and trailing slashes then split the path into segments
	    $segments = explode('/', trim($path, '/'));

	    // Remove the last segment
	    array_pop($segments);

	    // Reconstruct the URL without the last segment
	    if ($requires_base_url === true) {
	    	return BASE_URL . implode('/', $segments);
	    } else {
	    	return $str_start.implode('/', $segments);
	    }
	    
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

			$reduced_ref_dir = $this->remove_first_four_if_numeric(url_title(REF_DIR));
			if ($reduced_ref_dir === segment(1)) {
				$view_file_path = 'docs_ref_home.php';
			}

		}

		if ($view_file_path === '') {
			// Could this be an attempt to view a page from the reference section?
			$ref_chapter_first_semgment = $this->remove_first_four_if_numeric(url_title(REF_DIR));
			if (segment(1) === $ref_chapter_first_semgment) {
				$view_file_path = 'docs_feature_ref_content.php';
			}
			
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
	        $file_relative_path = str_replace(DIRECTORY_SEPARATOR, '/', $file_relative_path);
	        $file_relative_path = str_replace('\\', '/', $file_relative_path); // Ensure forward slashes
		$file_relative_path = str_replace(APPPATH, '', $file_relative_path);
		    
	        if (strpos($file_relative_path, REF_DIR) === false) {
		       $file_url_path = str_replace('_', '-', $file_relative_path); // Replace underscores with hyphens
		       $file_url_path = $this->remove_first_four_if_numeric($file_url_path);
		       $page_url = BASE_URL . ltrim($file_url_path, '/');
		       $page_url = $this->format_page_url($page_url);
	        } else {
	        	$file_url_path = $file_relative_path;
	        	$page_url = BASE_URL . ltrim($file_url_path, '/');
	        }

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

	    usort($html_files, function($a, $b) {
	        return strcmp($a['filename'], $b['filename']);
	    });

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
        $page_label = str_replace('What Are Templates', 'What Are Templates?', $page_label);
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
