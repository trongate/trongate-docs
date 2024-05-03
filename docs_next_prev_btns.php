<?php
$prev = $next_prev_data['prev'] ?? false;
$next = $next_prev_data['next'] ?? false;

if (($prev !== false) || ($next !== false)) {
?>
    <div class="page-nav-btns">
        <div>
        	<?php
        	if ($prev !== false) {
        		echo anchor($prev, '<i class="fa fa-arrow-circle-left"></i> Prev', array('class' => 'button'));
        	} else {
        		echo '&nbsp;';
        	}
        	?>
        </div>
        <div>
            <?php
        	if ($next !== false) {
        		echo anchor($next, 'next <i class="fa fa-arrow-circle-right"></i>', array('class' => 'button'));
        	} else {
        		echo '&nbsp;';
        	}
        	?>
        </div>
    </div>
<?php
}