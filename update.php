<?php
define('AUTHPATH',$_SERVER['SERVER_NAME']);

require "include/config/hostname.php";

// Check Hostname yang mengakses
new hostname();

require_once 'include/class_db/sync_db.php';

$schema = SCHEMA;

$status = sobad_db::_create_file_list($schema);
if($status==false){
	$status = sobad_db::_update_file_list($schema);
}

if($status){
	header('Location: '.SITE.'://'.HOSTNAME.'/'.URL.'/');
}else{
	echo 'Gagal Update !!!';
}