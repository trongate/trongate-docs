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
				$table_of_contents = $docs_contents->table_of_contents;
		    	foreach($table_of_contents as $chapter) {

		    		echo '<li>'.$chapter['dir_label'].'</li>';
		    		$files = $chapter['files'];

		    		echo '<ul>';
		    		foreach($files as $file) {
		    			echo '<li>'.anchor($file['page_url'], $file['label']).'</li>';
		    		}
		    		echo '</ul>';

		    	}
		    	?>
		    </ul>
		</aside>

		<main><?php
		include('docs_next_prev_btns.php');

		if (!file_exists($view_file)) {
			var_dump($view_file);
		} else {
			require_once($view_file);
			include('docs_next_prev_btns.php');
		}
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