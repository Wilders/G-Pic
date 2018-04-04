<?php
session_start();
require('inc/functions.class.php');
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
    <link href="css/dropzone.css" rel="stylesheet">
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
            <?php if(isset($_GET['error'])) {?><div class='mt-2 alert alert-dismissible alert-warning'><button type='button' class='close' data-dismiss='alert'>&times;</button><p class='mb-0'>ERREUR: Image inexistante et/ou non supprimée</p></div><?php } ?>
            <?php if(isset($_GET['ok'])) {?><div class='mt-2 alert alert-dismissible alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button><p class='mb-0'>OK: Image Supprimée</p></div><?php } ?>
            <div class="fallback" id="response"></div>
            <div class="card">
              <div class="card-body dropzone" id="drop-zone">
              </div>
              <div class="row">
                <div class="card-body text-center col-md-6">
                  <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      Total images uploadées
                      <span class="badge badge-info badge-pill"><?php $get = $bdd->prepare("SELECT uploads FROM stats WHERE id = '0'"); $get->execute(); while($data = $get->fetch()) { echo $data['uploads'];} $get->closeCursor(); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      Total de vues
                      <span class="badge badge-info badge-pill"><?php $get = $bdd->prepare("SELECT views FROM stats WHERE id = '0'"); $get->execute(); while($data = $get->fetch()) { echo $data['views'];} $get->closeCursor(); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      Total de téléchargements
                      <span class="badge badge-info badge-pill"><?php $get = $bdd->prepare("SELECT downloads FROM stats WHERE id = '0'"); $get->execute(); while($data = $get->fetch()) { echo $data['downloads'];} $get->closeCursor(); ?></span>
                    </li>
                  </ul>
                </div>
                <div class="card-body text-center col-md-6">
                  <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      Extensions acceptées
                      <span class="badge badge-info badge-pill">png, jpg, jpeg, gif, tif, tiff, tga</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      Taille maximale
                      <span class="badge badge-info badge-pill">5 Mo</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      En uplodant une image vous acceptez les C.G.U.
                      <a href="tos.php" target = "_blank" class="badge badge-info badge-pill">ICI</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-3">
         <?php 
          $get = $bdd->prepare("SELECT id,hash_name,uploaded_at FROM images WHERE public = 1 ORDER BY RAND() LIMIT 0,12");
          $get->execute();
          while($data = $get->fetch()) {?>
            <div class="col-md-3 mt-2">
                <div class="card">
                    <img class="card-img-top" src="uploads/<?php echo $data['hash_name']; ?>" alt="Image" width="100" height="150" style="object-fit: cover;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="view.php?id=<?php echo $data['id']; ?>" class="btn btn-sm btn-outline-info">Voir</a>
                                <?php if(isset($_SESSION['id'])) { ?>
                                <a href="delete.php?id=<?php echo $data['id']; ?>" class="btn btn-sm btn-outline-danger">Delete</a>
                                <?php } else {?>
                                <a href="dl.php?id=<?php echo $data['id']; ?>" class="btn btn-sm btn-outline-success">Download</a>
                                <?php } ?>
                            </div>
                            <small class="text-muted"><?php echo date('d/m/y H:i', strtotime($data['uploaded_at'])); ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <ol class="breadcrumb mt-2">
          <li class="breadcrumb-item active">&copy; g-pic | Made by <a href="http://wilders.fr">Wilders</a> | Site maintenu par l'équipe de <a href="http://g-box.fr">g-box.fr</a></li>
        </ol>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/holder.min.js"></script>
    <script src="js/dropzone.js"></script>
    <script>
        Dropzone.options.dropZone = {
            init: function() {
              this.on("success", function(file, responseText) {
                var issou = jQuery.parseJSON(JSON.stringify(responseText));
                if(issou['valid'] === true) {
                  document.getElementById('response').innerHTML = document.getElementById('response').innerHTML + "<div class='mt-2 alert alert-dismissible alert-info'><button type='button' class='close' data-dismiss='alert'>&times;</button><p class='mb-0'>Lien de partage de l'image : " + issou['shareUrl'] + "</p></div>";
                }
              });
            },
            url: "upload.php",
            paramName: "file",
            maxFilesize: 3,
            maxFiles : 5,
            parallelUploads : 1,
            acceptedFiles : ".jpeg,.jpg,.png,.gif,.bmp,.tif,.tiff,.tga",
            dictDefaultMessage : "<h3><p>Drag & Drop</p> <p>ou</p> <p>Clique ici pour upload ton image!</p></h3>",
        };
    </script>
  </body>
</html>