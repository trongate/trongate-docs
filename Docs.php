<?php
require_once('docs_config.php');
require_once('helpers.php');

class Docs {

	public function return_table_of_contents() {
        $path = APPPATH;

        $result = $this->get_sub_directories($path, EXCLUDE_DIRS);
        return $result;
	}

	private function get_sub_directories($path, $exclude_dirs = null) {
	    $sub_directories = [];

	    // Check if the directory exists and is a directory
	    if (is_dir($path)) {
	        // Open the directory
	        if ($dir_handle = opendir($path)) {
	            // Loop through the directory entries
	            while (false !== ($entry = readdir($dir_handle))) {
	                if (isset($exclude_dirs)) {
	                    $first_char = substr($entry, 0, 1);
	                    if ((in_array($entry, $exclude_dirs)) || ($first_char === '.')) {
	                        continue;
	                    }
	                }

	                // Skip '.' and '..' directories and ensure it is a directory
	                if ($entry != "." && $entry != ".." && is_dir($path . DIRECTORY_SEPARATOR . $entry)) {
	                    // Prepare directory label by removing leading numbers and replacing underscores
	                    $label = preg_replace('/^\d+/', '', $entry);  // Remove leading digits
	                    $label = str_replace('_', ' ', $label);       // Replace underscores with spaces
	                    $label = trim($label);                       // Trim extra spaces

	                    $sub_sub_dirs = $this->get_sub_directories($path . '/' . $entry);
	                    
	                    // Append the directory name and label to the result array
	                    $row_data['dir_name'] = $entry;
	                    $row_data['dir_label'] = ucwords($label);
	                    $row_data['sub_directories'] = $sub_sub_dirs;
	                    $row_data['files'] = $this->fetch_html_files($path.'/'.$entry);

	                    $sub_directories[] = $row_data;
	                }
	            }
	            closedir($dir_handle);
	        }
	    } else {
	        return "Error: The provided path is not a directory.";
	    }

	    // Sort the array alphabetically based on the directory name
	    usort($sub_directories, function($a, $b) {
	        return strcmp($a['dir_name'], $b['dir_name']);
	    });

	    return $sub_directories;
	}

	private function fetch_html_files($directory) {
	    $html_files = [];

	    // Check if the directory exists and is a directory
	    if (is_dir($directory)) {
	        // Open the directory
	        if ($dir_handle = opendir($directory)) {
	            // Loop through the directory entries
	            while (false !== ($file = readdir($dir_handle))) {
	                // Check if the file ends with .html
	                if (substr($file, -5) === '.html') {

	                	$row_data['filename'] = rtrim($file, '/');
	                	$row_data['filepath'] = $directory.'/'.$file;
	                	$row_data['filepath'] = str_replace(APPPATH.'/', APPPATH, $row_data['filepath']);
	                	$row_data['label'] = $this->build_nice_label($file);

	                	$page_url = str_replace(APPPATH, BASE_URL, $row_data['filepath']);
	                	$page_url = str_replace(BASE_URL, '', $page_url);

	                	$corrected_page_url = '';
	                	$page_url_bits = explode('/', $page_url);
	           	
	                	foreach($page_url_bits as $key => $page_url_bit) {

	                		$corrected_url_bit = strtolower($this->remove_first_four_if_numeric($page_url_bit));
	                		if ($key > 0) {
	                			$corrected_page_url.= '/';
	                		}

	                		$corrected_page_url.= $corrected_url_bit;
	                	}

	                	$corrected_page_url = BASE_URL.$corrected_page_url;
	                	$corrected_page_url = str_replace(BASE_URL.'/', BASE_URL, $corrected_page_url);
	                	$row_data['page_url'] = $corrected_page_url;

	                    $html_files[] = $row_data;
	                }
	            }
	            closedir($dir_handle);
	        }
	    } else {
	        echo "Error: The provided path '{$directory}' is not a valid directory.";
	    }

	    return $html_files;
	}

	public function get_url_path() {
		$url_path = str_replace(BASE_URL, '', current_url());
		$url_path = rtrim($url_path, '/');
		return $url_path;
	}

	function get_view_file_path($table_of_contents) {

		$view_file_path = '';
		foreach($table_of_contents as $chapter) {
			$files = $chapter['files'];
			foreach($files as $file) {
				if ($file['page_url'] === current_url()) {
					$view_file_path = $file['filepath'];
				}
			}
		}

		return $view_file_path;
	}

	private function get_real_file_name($containing_dir, $last_bit, $table_of_contents) {

		// Get the files within this containing dir.
		$containing_dir = rtrim($containing_dir, '/');
		$last_bit = strtolower($last_bit);
		$root_dir = APPPATH;

		foreach($table_of_contents as $chapter) {
			$chapter_dir = $root_dir.$chapter['dir_name'];
			$chapter_dir = rtrim($chapter_dir, '/');

			if ($chapter_dir === $containing_dir) {
				$files = $chapter['files'];

				foreach($files as $file) {

					$file = strtolower($file);
					$reduced_file = $this->remove_first_four_if_numeric($file);
					if ($reduced_file === $last_bit) {
						return $file;
					}
				}
			}

		}

		return $last_bit;
	}

	private function get_real_dir_name($segment, $table_of_contents) {

		$primary_dirs = [];
		foreach($table_of_contents as $chapter) {
			$real_dir_name = $chapter['dir_name'];
			$temp_dir_name = strtolower($this->remove_first_four_if_numeric($real_dir_name));
			$segment = strtolower($segment);

			if ($temp_dir_name === $segment) {
				return $real_dir_name;
			}

		}

		return $segment;
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

    public function draw_left_nav($table_of_contents) {
    	foreach($table_of_contents as $chapter) {
    		echo '<li>'.$chapter['dir_label'].'</li>';
    		$this->draw_pages_list($chapter, $table_of_contents);
    	}
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

	private function draw_alt_nav_list($directories) {
		foreach($directories as $directory) {
			foreach($directories as $sub_directory) {
				echo '<li>'.$sub_directory['dir_label'].'</li>';

				$files = $sub_directory['files'];
				$sub_directories = $sub_directory['sub_directories'];

				if (count($sub_directories) > 0) {
					echo '<ul>';
					$this->draw_alt_nav_list($sub_directories);
					echo '</ul>';
				} else {
					echo '<ul>';
					foreach($files as $file) {
						$page_label = str_replace('.html', '', $file);
						echo '<li>'.anchor('#', $page_label).'</li>';
					}
					echo '</ul>';
				}

			}
		}
	}

    private function draw_pages_list($chapter_data, $table_of_contents) {
    	$chapter_pages = $chapter_data['files'];
		echo '<ul>';

		if ($chapter_data['dir_name'] === REF_DIR) {
			$this->draw_alt_nav_list($chapter_data['sub_directories']);
		} else {
			$page_num = 0;
			foreach($chapter_pages as $chapter_page) {
				$page_num++;
				$page_label = $this->build_nice_label($chapter_page);
				$root_dir = APPPATH.$chapter_data['dir_name'].'/';
				$page_url = $this->est_page_url($chapter_page, $root_dir);

				if ($page_num === 1) {
					$page_url = BASE_URL;
				}
				echo '<li>'.anchor($page_url, $page_label).'</li>';
			}
		}

		echo '</ul>';
    }

    private function est_page_url($chapter_page, $root_dir=null) {
    	$root_url = str_replace(APPPATH, '', $root_dir);
    	$root_url = $this->remove_first_four_if_numeric($root_url);
    	$root_url = BASE_URL.$root_url;
    	$new_segment = $this->remove_first_four_if_numeric($chapter_page);
    	$page_url = strtolower($root_url.$new_segment);
    	return $page_url;
    }

}
