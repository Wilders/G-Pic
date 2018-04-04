<?php
require 'inc/functions.class.php';
require 'inc/settings.class.php';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_FILES['file'])) {
		$name     = $_FILES['file']['name'];
		$hash	  = md5($_FILES['file']['name'] . $time . $ip);
		$tmpName  = $_FILES['file']['tmp_name'];
		$size     = $_FILES['file']['size'];
		$ext	  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
		$fullName = $hash. '.'. $ext;
		$check = getimagesize($tmpName);
		if(!in_array($ext, $extensions)) {
			$header = header("HTTP/1.0 406 Not Acceptable");
			$answer = array('valid' => false, 'error' => 'Extension non permise.');
		}
		if($check == false) {
			$header = header("HTTP/1.0 415 Unsupported Media Type");
			$answer = array('valid' => false, 'error' => 'Le fichier n\'est pas une image.');
		}
		if($size > $maxsize) {
			$header = header("HTTP/1.0 413 Request Entity Too Large");
			$answer = array('valid' => false, 'error' => 'Le fichier est trop volumineux.');
		}
		if(!isset($answer)) {
			$targetPath =  dirname( __FILE__ ) . DIRECTORY_SEPARATOR. 'uploads' . DIRECTORY_SEPARATOR. $hash. '.'. $ext;
			move_uploaded_file($tmpName,$targetPath); 
			$insert = $bdd->prepare("INSERT INTO images (hash_name, uploaded_by) VALUES (:hash_name, :uploaded_by)");
			$insert->bindParam(':hash_name', $fullName);
			$insert->bindParam(':uploaded_by', $ip);
			$insert->execute();
			$insert->closeCursor();
			$get = $bdd->prepare("SELECT id,hash_name FROM images WHERE hash_name = :hash_name");
			$get->bindParam(':hash_name', $fullName);
			$get->execute();
			while($data = $get->fetch()) {
				$answer = array('valid' => true, 'shareUrl' => 'http://sambre.wilders.fr/view.php?id='. $data['id'], 'dlUrl' => 'http://sambre.wilders.fr/dl.php?id='. $data['id'], 'imageUrl' => 'http://sambre.wilders.fr/uploads/'. $data['hash_name']);
			}
			$get->closeCursor();
			$get = $bdd->prepare("SELECT uploads FROM stats WHERE id = '0'");
			$get->execute();
			while($data = $get->fetch()) {
				$uploads = $data['uploads'] + 1;
				$insert = $bdd->prepare("UPDATE stats SET uploads = :uploads WHERE id= '0'");
				$insert->bindParam(':uploads', $uploads);
				$insert->execute();
				$insert->closeCursor();
			}
			$get->closeCursor();
			
		}
		if(isset($header)) {
			header($header);
			header('Content-Type: application/json');
		} else {
			header('Content-Type: application/json');
		}
		if(isset($answer)) {
			echo json_encode($answer);
		} else {
			echo json_encode(array('valid' => false, 'error' => 'Erreur inconnue'));
		}
	}
	if(isset($_POST['base64']) && !empty($_POST['base64'])) {
		$image = base64_decode($_POST['base64'], true);
		if(!$image) {
			header('Content-Type: application/json');
			header("HTTP/1.0 415 Unsupported Media Type");
			echo json_encode(array('valid' => false, 'error' => 'Le base64 n\'a pas été reconnu'));
			die();
		}
		$length = strlen($image);
		if ( strpos($image, "<?php") !== false || $length === strlen(utf8_decode($image)) || $image[0].$image[1] !== "\xFF\xD8" || $image[$length-2].$image[$length-1] !== "\xFF\xD9" ) {
			header('Content-Type: application/json');
			header("HTTP/1.0 403 Forbidden");
			echo json_encode(array('valid' => false, 'error' => 'Nop :/'));
			die();
		}
		$finalName = uniqid("scr_", true).".jpg";
		if (!$handle = fopen("uploads/".$finalName, "w")) {
			header('Content-Type: application/json');
			header("HTTP/1.0 403 Forbidden");
			echo json_encode(array('valid' => false, 'error' => 'Impossible de créer le fichier'));
			die();
		}
		fwrite($handle, $image);
		fclose($handle);
		$insert = $bdd->prepare("INSERT INTO images (hash_name, uploaded_by) VALUES (:hash_name, :uploaded_by)");
		$insert->bindParam(':hash_name', $finalName);
		$insert->bindParam(':uploaded_by', $ip);
		$insert->execute();
		$insert->closeCursor();
		$get = $bdd->prepare("SELECT id,hash_name FROM images WHERE hash_name = :hash_name");
		$get->bindParam(':hash_name', $finalName);
		$get->execute();
		while($data = $get->fetch()) {
			header('Content-Type: application/json');
			echo json_encode(array('valid' => true, 'shareUrl' => 'http://sambre.wilders.fr/view.php?id='. $data['id'], 'dlUrl' => 'http://sambre.wilders.fr/dl.php?id='. $data['id'], 'imageUrl' => 'http://sambre.wilders.fr/uploads/'. $data['hash_name']));
		}
		$get->closeCursor();
		$get->closeCursor();
		$get = $bdd->prepare("SELECT uploads FROM stats WHERE id = '0'");
		$get->execute();
		while($data = $get->fetch()) {
			$uploads = $data['uploads'] + 1;
			$insert = $bdd->prepare("UPDATE stats SET uploads = :uploads WHERE id= '0'");
			$insert->bindParam(':uploads', $uploads);
			$insert->execute();
			$insert->closeCursor();
		}
		$get->closeCursor();
	}
}
?>