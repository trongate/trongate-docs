<?php
require_once('docs_config.php');
require_once('helpers.php');

class Docs {

	public $local_base_url = 'http://localhost/trongate-docs/';
	public $live_base_url = 'https://trongate.io/docs/';

	public function fetch_table_of_contents() {
		$target_dir = APPPATH.'trongate-docs';
		$directories = $this->get_directories($target_dir);
		$table_of_contents = $this->add_pages_arary($target_dir, $directories);
		$num_chapters = count($table_of_contents);
		unset($table_of_contents[$num_chapters-1]);
		$ref_chapter = $this->build_ref_chapter_tree();
		$table_of_contents[] = $ref_chapter;
		return $table_of_contents;
	}

	function build_ref_chapter_tree() {

/*
{
    "dir_name": "0010Introduction",
    "dir_label": "Introduction",
    "pages": [
        {
            "page_name": "0005home.html",
            "page_label": "Home",
            "page_url": "http:\/\/localhost\/trongate-docs\/introduction\/home.html"
        },
        {
            "page_name": "0007table_of_contents.html",
            "page_label": "Table Of Contents",
            "page_url": "http:\/\/localhost\/trongate-docs\/introduction\/table_of_contents.html"
        },
        {
            "page_name": "0010welcome.html",
            "page_label": "Welcome",
            "page_url": "http:\/\/localhost\/trongate-docs\/introduction\/welcome.html"
        }
    ]
}
*/
	
		$data['dir_name'] = '0300Comprehensive_Reference_Guide';
		$data['dir_label'] = 'Comprehensive Reference Guide';

		$dir_path = APPPATH.'trongate-docs/0300Comprehensive_Reference_guide';
		$data['sub_directories'] = $this->get_sub_directories($dir_path);
		return $data;

	}

	private function get_sub_directories($path) {
	    $sub_directories = [];

	    
	    // Check if the directory exists and is a directory
	    if (is_dir($path)) {
	        // Open the directory
	        if ($dir_handle = opendir($path)) {
	            // Loop through the directory entries
	            while (false !== ($entry = readdir($dir_handle))) {
	                // Skip '.' and '..' directories
	                if ($entry != "." && $entry != ".." && is_dir($path . DIRECTORY_SEPARATOR . $entry)) {
	                    // Prepare directory label by removing leading numbers and replacing underscores
	                    $label = preg_replace('/^\d+/', '', $entry);  // Remove leading digits
	                    $label = str_replace('_', ' ', $label);       // Replace underscores with spaces
	                    $label = trim($label);                       // Trim extra spaces

	                    $sub_sub_dirs = $this->get_sub_directories($path.'/'.$entry);
	                    
	                    // Append the directory name and label to the result array

	                    $row_data['dir_name'] = $entry;
	                    $row_data['dir_label'] = ucwords($label);
	                    $row_data['sub_directories'] = $sub_sub_dirs;

	                    $sub_directories[] = $row_data;

	                }
	            }
	            closedir($dir_handle);
	        }
	    } else {
	        return "Error: The provided path is not a directory.";
	    }

	    return $sub_directories;
	}

	public function fetch_chapter_name_num($url) {
		$chapter_num = 0;
		$table_of_contents = $this->fetch_table_of_contents();
		foreach($table_of_contents as $chapter) {
			$chapter_num++;
			$chapter_pages = $chapter['pages'];
			foreach($chapter_pages as $chapter_page) {

				if (strpos($chapter_page['page_url'] , $url) !== false) {
					$data['chapter_name'] = $chapter['dir_label'];
					$data['chapter_num'] = $chapter_num;
					return $data;					
				}
	
			}

		}

		return false;
	}

	public function get_real_file_name($dir_real_name, $target_file_name) {
		$target_file_name = strtolower($target_file_name);
		$target_dir = $root_dir = APPPATH.'trongate-docs/'.$dir_real_name;

		$files_in_dir = $this->get_html_files($target_dir);
		foreach($files_in_dir as $file_in_dir) {
			$real_file_name = $file_in_dir['page_name'];
			$nice_file_name = $this->remove_first_x_characters($real_file_name, 4);
			$nice_file_name = strtolower($nice_file_name);

			if ($target_file_name === $nice_file_name) {
				return $real_file_name;
			}
		}
		return false;
	}

	public function get_dir_real_name($target_dir_name) {
		$target_dir = strtolower($target_dir_name);
		$root_dir = APPPATH.'trongate-docs';
		$directories = $this->get_directories($root_dir);
		
		foreach($directories as $directory) {
			$nice_name = $this->remove_first_x_characters($directory, 4);
			$nice_name = strtolower($nice_name);
			
			if ($nice_name === $target_dir) {
				$real_dir_name = $directory;
				return $real_dir_name;
			}
		}

		return false;
	}

	public function add_pages_arary($app_dir, $directories) {

		$table_of_contents = [];
		foreach($directories as $directory) {
			$row_data['dir_name'] = $directory;
			$target_dir = $app_dir.'/'.$row_data['dir_name'];
			
			$row_data['dir_label'] = str_replace('_', ' ', $this->remove_first_x_characters($directory, 4));
			$row_data['pages'] = $this->get_html_files($target_dir);

			$table_of_contents[] = $row_data;
		}

		return $table_of_contents;
	}

	public function build_page_url($chapter_page, $directory_name) {
		$directory_name = str_replace(APPPATH, '', $directory_name);

		//$str = 'trongate-docs/';
		$str_start = substr($directory_name, 0, 14);
		
		if ($str_start === 'trongate-docs/') {
			$directory_name = $this->remove_first_x_characters($directory_name, 14);
		}

		if (strpos(current_url(), 'localhost') !== false) {
			$base_url = $this->local_base_url;
		} else {
			$base_url = $this->live_base_url;
		}

	    // Remove the first 4 characters (.html) from the page name and directory name
	    $page_name = $this->remove_first_x_characters($chapter_page['page_name'], 4);
	    $dir_name = $this->remove_first_x_characters($directory_name, 4);

	    // Construct and return the full URL for the page
	    $page_url = $base_url . strtolower($dir_name) . '/' . $page_name;
	    return $base_url . strtolower($dir_name) . '/' . $page_name;
	}


	/**
	 * Retrieves directories from the specified path, excluding certain directories.
	 *
	 * @param string $path The path to the directory.
	 * @return array An array containing directories within the specified path.
	 */
	public function get_directories(string $path): array
	{
	    // Directories to ignore
	    $ignore_dirs = ['css', 'images', 'js', 'fonts', 'junk'];

	    // Initialize an empty array to store directories
	    $directories = [];

	    // Check if the path is a valid directory
	    if (is_dir($path)) {
	        // Open the directory
	        if ($handle = opendir($path)) {
	            // Loop through each item in the directory
	            while (($item = readdir($handle)) !== false) {
	                // Exclude special directories (. and ..)
	                if ($item != '.' && $item != '..') {
	                    // Check if the item is a directory
	                    if (is_dir($path . '/' . $item)) {
	                        // Add the directory to the array
	                        if (!in_array($item, $ignore_dirs)) {
	                            $directories[] = $item;
	                        }
	                    }
	                }
	            }
	            // Close the directory handle
	            closedir($handle);

	            // Sort the directories alphabetically
	            sort($directories);
	        }
	    } else {
	        // Output an error message if the path is not a valid directory
	        echo "Error: '$path' is not a valid directory.";
	    }

	    // Remove the first element of the array (current directory)
	    unset($directories[0]);

	    // Return the array of directories
	    return $directories;
	}

	function get_html_files_alpha($directory) {
		// Similar to get_html_files() but returns files in alphabetical order

		$found_files = [];

	    // Check if the directory exists and is readable
	    if (is_dir($directory) && is_readable($directory)) {
	        // Open the directory
	        if ($handle = opendir($directory)) {
	            // Loop through each item in the directory
	            while (($item = readdir($handle)) !== false) {
	                // Exclude special directories (. and ..)
	                if ($item != '.' && $item != '..') {
	                    // Check if the item is a file ending with .html extension
	                    if (is_file($directory . '/' . $item) && pathinfo($item, PATHINFO_EXTENSION) === 'html') {
	                        // Add the html file to the array
	                        $found_files[] = $item;
	                    }
	                }
	            }
	            // Close the directory handle
	            closedir($handle);

	            // Sort the html files alphabetically
	            sort($found_files);
	        }
	    } else {
	        // Output an error message if the directory is not valid or readable
	        echo "Error: '$directory' is not a valid or readable directory.";
	    }

	    $html_files = [];
	    foreach($found_files as $found_file) {
	    	$row_data['page_name'] = $found_file;
	    	$row_data['page_label'] = $this->prep_page_label($found_file);
	    	$row_data['page_url'] = current_url().$found_file;
	    	$html_files[] = $row_data;
	    }

	    return $html_files;

	}

	function get_html_files($directory) {

	    // Initialize an empty array to store html files
	    $html_files = [];

	    // Check if the directory exists and is readable
	    if (is_dir($directory) && is_readable($directory)) {
	        // Open the directory
	        if ($handle = opendir($directory)) {
	            // Loop through each item in the directory
	            while (($item = readdir($handle)) !== false) {
	                // Exclude special directories (. and ..)
	                if ($item != '.' && $item != '..') {
	                    // Check if the item is a file ending with .html extension
	                    if (is_file($directory . '/' . $item) && pathinfo($item, PATHINFO_EXTENSION) === 'html') {
	                        // Add the html file to the array
	                        $html_files[] = $item;
	                    }
	                }
	            }
	            // Close the directory handle
	            closedir($handle);

	            // Sort the html files alphabetically
	            sort($html_files);
	        }
	    } else {
	        // Output an error message if the directory is not valid or readable
	        echo "Error: '$directory' is not a valid or readable directory.";
	    }

	    // Return the array of html files

	    $data = [];
	    foreach($html_files as $html_file) {
	    	$row_data['page_name'] = $html_file;
	    	$row_data['page_label'] = str_replace('_', ' ', $this->remove_first_x_characters($html_file, 4));
	    	$row_data['page_label'] = $this->remove_last_x_characters($row_data['page_label'], 5);
	    	$row_data['page_label'] = ucwords($row_data['page_label']);

	    	$row_data['page_url'] = $this->build_page_url($row_data, $directory);
	    	$data[] = $row_data;
	    }

	    return $data;
	}

	/**
	 * Removes the first x characters from the given string.
	 *
	 * @param string $string The input string.
	 * @param int $num The number of characters to remove.
	 * @return string The modified string.
	 */
	function remove_first_x_characters(string $string, int $num): string {
	    // Check if the string length is greater than $num
	    if (strlen($string) > $num) {
	        // Remove the first $num characters and return the result
	        return substr($string, $num);
	    } else {
	        // Return the original string if its length is $num or less
	        return $string;
	    }
	}

	/**
	 * Removes the last x characters from the given string.
	 *
	 * @param string $string The input string.
	 * @param int $num The number of characters to remove.
	 * @return string The modified string.
	 */
	function remove_last_x_characters(string $string, int $num): string {
	    // Check if the string length is greater than the specified number
	    if (strlen($string) > $num) {
	        // Remove the last $num characters and return the result
	        return substr($string, 0, -$num);
	    } else {
	        // Return the original string if its length is less than or equal to $num
	        return $string;
	    }
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

    static public function display($data = null) {

        if (!isset($data['view_file'])) {
            $data['view_file'] = 'index';
        }

        $data['view_file'].= '.php';

        $file_path = APPPATH.$data['view_file'];

//        $file_path = APPPATH . 'modules/' . $data['view_module'] . '/views/' . $data['view_file'] . '.php';
        //self::attempt_include($file_path, $data);
        include($file_path);
    }

    private function prep_page_label($page_label) {
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
        
        return $page_label;
    }

    private function draw_pages_list($chapter_data) {
    	$chapter_pages = $chapter_data['pages'];
					echo '<ul>';
					foreach($chapter_pages as $chapter_page) {
						$page_name = $chapter_page['page_name'];
						$page_label = $chapter_page['page_label'];
						$dir_name = $this->remove_first_x_characters($chapter_data['dir_name'], 4);
						$page_name = $this->remove_first_x_characters($page_name, 4);
						$page_url = BASE_URL.strtolower($dir_name).'/'.$page_name;
		                $page_label = $this->prep_page_label($page_label);
						echo '<li>'.anchor($page_url, $page_label).'</li>';
					}
					echo '</ul>';
    }

    private function draw_sub_directories_list($parent_dir_name, $sub_directories) {

		/*
			{
			    "dir_name": "0300Comprehensive_Reference_Guide",
			    "dir_label": "Comprehensive Reference Guide",
			    "sub_directories": [
			        {
			            "dir_name": "Helpers",
			            "dir_label": "Helpers",
			            "sub_directories": [
			                {
			                    "dir_name": "String_Helpers",
			                    "dir_label": "String Helpers",
			                    "sub_directories": []
			                }
			            ]
			        }
			    ]
			}
		*/

		echo '<ul>';
		foreach($sub_directories as $sub_directory) {
			$dir_name = $sub_directory['dir_name'];
			$page_url = BASE_URL.$parent_dir_name.'/'.$dir_name;

			$reduced_page_url = str_replace(BASE_URL, '', $page_url);
			$reduced_page_url = rtrim($reduced_page_url, '/');
			$url_segments = explode('/', $reduced_page_url);

			if (count($url_segments) > 2) {
				echo '<li>'.anchor($page_url, $dir_name).'</li>';
			} else {
				echo '<li><b>'.$dir_name.'</b></li>';
			}
			

			$new_parent_dir_name = $parent_dir_name.'/'.$dir_name;

			$this->draw_sub_directories_list($new_parent_dir_name, $sub_directory['sub_directories']);
		}
		echo '</ul>';
    }

}