<?php
session_start();
if(isset($_SESSION['id'])) {
  header('Location: index.php');
  die();
}
require('inc/functions.class.php');
if(isset($_POST['u']) && isset($_POST['p'])) {
  if(!empty($_POST['u']) && !empty($_POST['p'])) {
    $sql = "SELECT id, username, password FROM users WHERE username = :username ";
    $stmt = $bdd->prepare($sql);
    $stmt->bindValue(':username', $_POST['u']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user === false) {
      $error = "Nom d'utilisateur inconnu.";
    } else {
      if($user['password'] == sha1($_POST['p'])) {
        $_SESSION['id'] = $user['id'];
        header('Location: index.php');
      } else {
        $error = "Mot de passe incorrect.";
      }
    }
  }
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
    <title>G-Pic | Login Admin</title>
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
  </head>
  <body>
    <div class="login">
      <h1>Connexion</h1>
      <?php if(isset($error)) {?><p class="mt-2"><?php echo $error; ?></p><?php } ?>
        <form action="#" method="POST">
          <input type="text" name="u" placeholder="Pseudonyme" required="required" />
            <input type="password" name="p" placeholder="Mot de passe" required="required" />
            <button type="submit" class="btn btn-primary btn-block btn-large">Connexion</button>
        </form>
        <a href="/" style="color: var(--info);"><h5 class="mt-2"><i class="fas fa-chevron-circle-left"></i> Retour</h5></a>
    </div>
  </body>
</html>