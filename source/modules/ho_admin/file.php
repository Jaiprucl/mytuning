<?php 
	// $download sei der Bezeichner f�r die zu ladende Datei
	// etwa: 
	$download = $_GET['get'];
	 
	// Dieses Verzeichnis liegt au�erhalb des Document Root und
	// ist nicht per URL erreichbar.
	$basedir = "../../";

	// Vertrauensw�rdigen Dateinamen basteln.
	$filename = sprintf("%s/%s", $basedir, $download);
	
 	// Passenden Datentyp erzeugen.
	header("Content-Type: application/octet-stream");
	 
	// Passenden Dateinamen im Download-Requester vorgeben,
	// z. B. den Original-Dateinamen
	$save_as_name = basename($_GET['get']);
	header("Content-Disposition: attachment; filename=\"$save_as_name\"");
	 
	// Datei ausgeben.
	readfile($filename);
?>