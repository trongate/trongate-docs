<?php
if (count($breadcrumbs_array) > 0) { ?>
<div aria-label="Breadcrumb" class="breadcrumbs sm"><?php

foreach($breadcrumbs_array as $breadcrumb_key => $breadcrumb_link) {
	if ($breadcrumb_key !== 0) {
		echo '<span>&raquo;</span>';
	}

	if ($breadcrumb_key !== count($breadcrumbs_array)-1) {
		echo '<div>'.anchor($breadcrumb_link['page_url'], $breadcrumb_link['label']).'</div>';
	} else {
		echo '<div>'.$breadcrumb_link['label'].'</div>';
	}
}

echo '</div>';
}