<?php
session_start();
require 'inc/functions.class.php';
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id = $_GET['id'];
	$try = $bdd->prepare("SELECT COUNT(id) AS num FROM images WHERE id = :id");
	$try->bindValue(':id', $id);
	$try->execute();
	$row = $try->fetch(PDO::FETCH_ASSOC);
	if($row['num'] > 0){
		$get = $bdd->prepare("SELECT hash_name,downloads FROM images WHERE id = :id");
		$get->bindParam(':id', $id);
		$get->execute();
		while($data = $get->fetch()) {
			$filename = $data['hash_name'];
			$downloads = $data['downloads'] + 1;
		}
		$get->closeCursor();
		if(!isset($_SESSION['downloads'][$id])) {
			$insert = $bdd->prepare("UPDATE images SET downloads = :downloads WHERE id = :id");
			$insert->bindParam(':downloads', $downloads);
			$insert->bindParam(':id', $id);
			$insert->execute();
			$insert->closeCursor();
			$get = $bdd->prepare("SELECT downloads FROM stats WHERE id = '0'");
			$get->execute();
			while($data = $get->fetch()) {
				$downloads = $data['downloads'] + 1;
			}
			$get->closeCursor();
			$insert = $bdd->prepare("UPDATE stats SET downloads = :downloads WHERE id = '0'");
			$insert->bindParam(':downloads', $downloads);
			$insert->execute();
			$insert->closeCursor();
			$_SESSION['downloads'][$id] = 1;
		}
		$filepath = "uploads/" . $filename;
	    if(file_exists($filepath)) {
	        header('Content-Description: File Transfer');
	        header('Content-Type: application/octet-stream');
	        header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
	        header('Expires: 0');
	        header('Cache-Control: must-revalidate');
	        header('Pragma: public');
	        header('Content-Length: ' . filesize($filepath));
	        flush();
	        readfile($filepath);
	        exit;
	    }
	} else {
		die();
	}
}
?>