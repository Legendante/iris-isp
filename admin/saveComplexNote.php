<?php
session_start();
include("db.inc.php");

$NoteArr = array();
$ComplexID = pebkac($_POST['noteComplexID']);
$NoteArr['complexid'] = $ComplexID;
$NoteArr['commentary'] = pebkac($_POST['compnote'], 1000, 'STRING');
$NoteArr['userid'] = $_SESSION['userid'];
addComplexNote($NoteArr);

header("Location: complex.php?cid=" . $ComplexID);
?>