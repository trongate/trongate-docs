<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/trongate.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/docs.css">
	<title>Document</title>
</head>
<body>
	<aside>
		<ul id="chapter-nav">
		<?php
		foreach($table_of_contents as $chapter_data) {
			$chapter_label = $chapter_data['dir_label'];
			echo '<li>'.$chapter_label.'</li>';
			$chapter_pages = $chapter_data['pages'];
			echo '<ul>';
			foreach($chapter_pages as $chapter_page) {
				$page_name = $chapter_page['page_name'];
				$page_label = $chapter_page['page_label'];
				$dir_name = $docs->remove_first_x_characters($chapter_data['dir_name'], 4);
				$page_name = $docs->remove_first_x_characters($page_name, 4);
				$page_url = BASE_URL.strtolower($dir_name).'/'.$page_name;
				echo '<li>'.anchor($page_url, $page_label).'</li>';
			}
			echo '</ul>';
		}
		?>
	    </ul>
	</aside>
	<main><?php
	require_once($view_file);
	?></main>
	<footer>
		footer to go here
	</footer>
</body>
</html>