<?php

require_once '../config.php';
require_once '../functions.php';

session_start();

checkToken();

$dbh = connectDb();
$id = (int)$_POST['id'];

$dbh->query("update entries set status = 'deleted' where id = $id");

echo $id;