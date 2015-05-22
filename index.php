<?php

   // Wir initialisieren die Variablen, in denen später Benutzername und E-Mail-Adresse gespeichert werden.
   $username = $email = $create_account = false;
   $username_class = $email_class = 'form-group';

   // Die Datei, in der die Benutzerdaten gespeichert werden.
   $filename = 'subscribe.csv';

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $username = filter_var($_POST['username'], FILTER_SANITIZE_SPECIAL_CHARS);
      $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
      $create_account = (int) filter_var($_POST['create_account'], FILTER_VALIDATE_BOOLEAN);
      $is_email_valid = $email && filter_var($email, FILTER_VALIDATE_EMAIL);

      if (!$username) $username_class .= ' has-error';
      if (!$is_email_valid) $email_class .= ' has-error';

      if (!file_exists($filename)) {
         $template = "Name, E-Mail, Account anlegen?, Datum\n";
         file_put_contents($filename, $template);
      }

      if ($username && $is_email_valid) {
         $content = "$username, $email, $create_account, " . time();

         $file = fopen($filename, 'a');
         $values = array($username, $email, $create_account, time());
         fputcsv($file, $values);
         fclose($file);

         $success = true;
      }

   }
?>

<!doctype html>
<html lang="de">

<head>
   <meta charset="UTF-8">
   <title>Subscribe</title>
   <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
   <div class="container">
      <div class="row">
         <div class="main">
            <div class="panel panel-primary">
               <div class="panel-heading">
                  <h3 class="panel-title">Auf dem Laufenden bleiben</h3>
               </div>
               <div class="panel-body">

               <?php if (isset($success)) : ?>
                  <div class="alert alert-success">
                     <strong>Erfolgreich eingetragen.</strong> Danke für dein Interesse!
                  </div>
               <?php endif; ?>

                  <form role="form" action="index.php" method="POST">
                     <div class="<?= $username_class ?>">
                        <label class="control-label">Bitte gib deinen Namen an.</label>
                        <input type="text" class="form-control" name="username" placeholder="Name" value="<?= $username ?>">
                     </div>
                     <div class="<?= $email_class ?>">
                        <label class="control-label">Bitte gib <?= $email && !$is_email_valid ? 'eine gültige' : 'deine' ?> E-Mail-Adresse an.</label>
                        <input type="text" class="form-control" name="email" placeholder="E-Mail-Adresse" value="<?= $email ?>">
                     </div>
                     <button type="submit" class="btn btn-primary pull-right">Jetzt Einschreiben</button>
                     <div class="checkbox">
                        <label>
                           <input type="checkbox" name="create_account" <?= $create_account ? 'checked' : '' ?>> Zugansdaten zusenden
                        </label>
                     </div>
                  </form>

               </div> <!-- /.panel-body -->
            </div> <!-- /.panel -->
         </div> <!-- /.main -->
      </div> <!-- /.row -->
   </div> <!-- /.container -->
</body>

</html>
