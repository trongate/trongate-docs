<?php
$next_prev_data['prev'] = $chapter_intro_data['prev_page'];
$next_prev_data['next'] = $chapter_intro_data['first_page'];
include('docs_next_prev_btns.php');
?>
<div class="chapter-num">Chapter <?= $chapter_intro_data['chapter_num'] ?></div>
<h1 id="ridiculously-huge"><?= $chapter_intro_data['chapter_name'] ?></h1>
<?php
$next_prev_data['prev'] = false;
$next_prev_data['next'] = false;
?>