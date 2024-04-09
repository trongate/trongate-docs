<?php
require_once('docs_config.php');
require_once('helpers.php');

class Docs {

	public function fetch_table_of_contents() {
		$target_dir = APPPATH.'trongate-docs';
		$directories = $this->get_directories($target_dir);



		$table_of_contents = $this->add_pages_arary($target_dir, $directories);
		return $table_of_contents;
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

	public function get_directories($path) {

		$ignore_dirs[] = 'css';

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

	    // Return the array of directories
	    unset($directories[0]);

	    // foreach($directories as $key => $value) {
	    // 	$directories[$key] = $this->remove_first_x_characters($value, 4);
	    // }

	    return $directories;
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
	    	$data[] = $row_data;
	    }


	    return $data;
	}

	function remove_first_x_characters($string, $num) {
	    // Check if the string length is greater than 3
	    if (strlen($string) > $num) {
	        // Remove the first 3 characters and return the result
	        return substr($string, $num);
	    } else {
	        // Return the original string if its length is 3 characters or less
	        return $string;
	    }
	}

	function remove_last_x_characters($string, $num) {
	    // Check if the string length is greater than the specified number
	    if (strlen($string) > $num) {
	        // Remove the last x characters and return the result
	        return substr($string, 0, -$num);
	    } else {
	        // Return the original string if its length is less than or equal to the specified number
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

}