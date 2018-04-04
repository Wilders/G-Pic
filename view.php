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
		$get = $bdd->prepare("SELECT hash_name,views,uploaded_at,downloads FROM images WHERE id = :id");
		$get->bindParam(':id', $id);
		$get->execute();
		while($data = $get->fetch()) {
			$filename = $data['hash_name'];
			$fileviews = $data['views'] + 1;
			$views = $data['views'];
			$uploaded_at = $data['uploaded_at'];
			$downloads = $data['downloads'];
		}
		$get->closeCursor();
		if(!isset($_SESSION['views'][$id])) {
			$insert = $bdd->prepare("UPDATE images SET views = :views, activity = NOW() WHERE id = :id");
			$insert->bindParam(':views', $fileviews);
			$insert->bindParam(':id', $id);
			$insert->execute();
			$insert->closeCursor();
			$get = $bdd->prepare("SELECT views FROM stats WHERE id = '0'");
			$get->execute();
			while($data = $get->fetch()) {
				$viewss = $data['views'] + 1;
			}
			$get->closeCursor();
			$insert = $bdd->prepare("UPDATE stats SET views = :views WHERE id = '0'");
			$insert->bindParam(':views', $viewss);
			$insert->execute();
			$insert->closeCursor();
			$_SESSION['views'][$id] = 1;
		} else {
			$insert = $bdd->prepare("UPDATE images SET activity = NOW() WHERE id = :id");
			$insert->bindParam(':id', $id);
			$insert->execute();
			$insert->closeCursor();
		}
	} else {
		header('Location: index.php');
	}
} else {
	header('Location: index.php');
}
?>
<!doctype html>
<html lang="fr-FR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.ico">
    <title>G-Pic</title>
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-light">
          <a class="navbar-brand" href="/"><i class="fas fa-camera-retro"></i>&nbsp; G-Pic</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item">
                <a class="nav-link" href="/">Héberger</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="addon.php">Addon</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.php">A propos</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="tos.php">C.G.U.</a>
              </li>
            </ul>
          </div>
        </nav>
        <div class="row mt-3">
          	<div class="col-md-12">
				<div class="card">
	  				<div class="card-header"><?php echo $views; ?> vues | <?php echo $downloads; ?> téléchargements | <?php echo date('d/m/y H:i', strtotime($uploaded_at)); ?></div>
	  				<div class="card-body">
	  					<div class="d-flex justify-content-center">
	  						<img src ="uploads/<?php echo $filename; ?>" class="img-fluid" style="max-height: 30rem;">
	  					</div>
	  					<p class="text-center mt-2">
	  						<a href="dl.php?id=<?php echo $id; ?>" class="btn btn-info btn-lg">Télécharger</a>
	  						<?php if(isset($_SESSION['id'])) {?> <a href="delete.php?id=<?php echo $id; ?>" class="btn btn-danger btn-lg">Supprimer</a><?php } ?>
	  					</p>
	  				</div>
				</div>
			</div>
		</div>
        <ol class="breadcrumb mt-2">
          <li class="breadcrumb-item active">&copy; g-pic | Made by <a href="http://wilders.fr">Wilders</a> | Site maintenu par l'équipe de <a href="http://g-box.fr">g-box.fr</a></li>
        </ol>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/holder.min.js"></script>
  </body>