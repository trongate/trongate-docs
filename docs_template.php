<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/trongate.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/docs.css">
	<title>Trongate Docs</title>
</head>
<body>

	<header>
		<div id="header-lg">
			<div class="container container-lg">
                <div class="trongate-logo" onclick="goToDocsHome('<?= BASE_URL ?>')">
                    <div id="logo-box-container">
                        <div id="logo-box1"></div>
                        <div id="logo-box2">
                            <div id="logo-box2-inner"></div>
                        </div>
                        <div id="logo-box3"></div>
                    </div>
                    <p>trOnGAtE</p>
                </div>
			</div>
		</div>
		<div id="header-sm">
			Header Small Ahoy!
		</div>
	</header>

	<div class="container container-lg">
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
	                $page_label = str_replace('rongate1', 'rongate #1', $page_label);
	                $page_label = str_replace('rongate2', 'rongate #2', $page_label);
	                $page_label = str_replace('rongate3', 'rongate #3', $page_label);
	                $page_label = str_replace('rongate4', 'rongate #4', $page_label);
	                $page_label = str_replace('rongate5', 'rongate #5', $page_label);
	                $page_label = str_replace('And', '&amp;', $page_label);
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
	</div>


	<footer>&nbsp;</footer>


	<script src="<?= BASE_URL ?>js/docs.js"></script>
</body>
</html>