<?php
require_once('Docs.php');
$docs = new Docs;

$table_of_contents = $docs->return_table_of_contents();
?>