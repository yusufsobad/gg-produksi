<?php
//(!defined('AUTHPATH'))?exit:'';

// ---------------------------------------
// Set Time Jakarta ----------------------
// ---------------------------------------
ini_set('date.timezone', 'Asia/Jakarta');

// Database -------------------------------------------

// set Server
define('SERVER',"localhost");

// set Username
define('USERNAME',"root");

// set Password
define('PASSWORD','');

// set Database
define('DB_NAME','ggkartosura_2021');
$GLOBALS['DB_NAME'] = DB_NAME;

// set rule database
$database_sc = array(
	0 => array(
		'db' 	=> DB_NAME, // nama database
		'where'	=> '' // TABLE_NAME= . . .
	)
);

define('SCHEMA',serialize($database_sc));

// URL web --------------------------------------------

// set hostname
define('SITE','http');

// set hostname
define('HOSTNAME',$_SERVER['SERVER_NAME']);

// set name url
define('URL','gg-produksi');

// set check table
define('ABOUT','');

// Setting -------------------------------------------

// prefix SESSION
define('_prefix','ggKartosura_');
		
// authentic include
define('AUTH_KEY','qJB0rGtInG03efyCpWs');

// PATH default
define('DEFPATH',dirname(__FILE__));

// set Multiple language
define('language',true);

// set nama Perusahaan
define('company','Solo Abadi System Information');

// set judul Website
define('title','System Produksi');

// Library ------------------------------------------
$library_sc = array(
	// name folder 		=> lokasi file,
	'createpdf'			=> 'html2pdf/html2pdf.class.php'
);

<<<<<<< HEAD
define('_library',array(
			// name folder 		=> lokasi file,
			'createpdf'			=> 'html2pdf/html2pdf.class.php'
		));
=======
define('_library',serialize($library_sc));
>>>>>>> 8f72827d3f5a390b22fb3435e4d0641af763aa7e
