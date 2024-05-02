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
				
				if (isset($chapter_data['pages'])) {
					$docs->draw_pages_list($chapter_data);
				} else {
					// Draw a list of sub directories.
					$docs->draw_sub_directories_list($chapter_data['dir_name'], $chapter_data['sub_directories']);
				}

			}
			?>
		    </ul>
		</aside>

		<main><?php
	    require_once($view_file);
	    ?></main>		
	</div>

	<footer>&nbsp;</footer>

<div class="modal" id="temp-modal" style="display: none">
	<div class="modal-body framework-ref">
		<div class="spinner mt-3 mb-3"></div>
	</div>
</div>


	<script>
		const baseUrl = '<?= BASE_URL ?>';
	</script>
	<script src="<?= BASE_URL ?>js/app.js"></script>
	<script src="<?= BASE_URL ?>js/docs.js"></script>
</body>
</html>