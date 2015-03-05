<?php
  $db = new SQLite3('bitcoin.db');

  if (is_numeric($_POST['phoneOrEmail'])) {
    $removalString = "DELETE FROM textUsers WHERE phone_number = '$_POST[phoneOrEmail]'";
    $db->exec($removalString);
  }
  if (filter_var($_POST['phoneOrEmail'], FILTER_VALIDATE_EMAIL)) {
    $removalString = "DELETE FROM emailUsers WHERE email = '$_POST[phoneOrEmail]'";
    $db->exec($removalString);
  }
  header("Location: project.html");
?>