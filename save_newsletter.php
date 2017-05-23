<?php
  require_once('classes/newsletter.class.php');

  $newsletter = new Newsletter();
  $email = $_GET['email'];

  try {
        if($newsletter->save($email)) {
          echo "<p>Obrigado! Seu email foi cadastrado com sucesso! $salvo</p>";
        } else {
          echo "<p>Houve um erro, tente novamente mais tarde.</p>";
        }
  } catch (Exception $e) {
    echo "<p class='alert-warning'>" . $e->getMessage() ."</p>";
  }
?>
