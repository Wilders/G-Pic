<?php
session_start();
require 'inc/functions.class.php';
if(!isset($_SESSION['id'])) {
	header('Location: index.php');
	die();
}
if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
	$id = $_GET['id'];
	$try = $bdd->prepare("SELECT COUNT(id) AS num FROM images WHERE id = :id");
	$try->bindValue(':id', $id);
	$try->execute();
	$row = $try->fetch(PDO::FETCH_ASSOC);
	if($row['num'] > 0){
		$get = $bdd->prepare("SELECT hash_name, views, downloads FROM images WHERE id = :id");
		$get->bindValue(':id', $id);
		$get->execute();
		while($data = $get->fetch()) {
			$hashname = $data['hash_name'];
			$views = $data['views'];
			$downloads = $data['downloads'];
		}
		$get->closeCursor();
		$del = $bdd->prepare("DELETE FROM images WHERE id = :id");
		$del->bindParam(':id', $id);
		$del->execute();
		$get = $bdd->prepare("SELECT uploads, views, downloads FROM stats WHERE id = '0'");
		$get->execute();
		while($data = $get->fetch()) {
			$uploads = $data['uploads'] - 1;
			$downloads = $data['downloads'] - $downloads;
			$views = $data['views'] - $views;
			$insert = $bdd->prepare("UPDATE stats SET uploads = :uploads, views = :views, downloads = :downloads WHERE id= '0'");
			$insert->bindParam(':uploads', $uploads);
			$insert->bindParam(':downloads', $downloads);
			$insert->bindParam(':views', $views);
			$insert->execute();
			$insert->closeCursor();
		}
		$get->closeCursor();
		if(unlink("uploads/".$hashname)) {
			header('Location: index.php?ok');
		}
	} else {
		header('Location: index.php?error');
	}
}