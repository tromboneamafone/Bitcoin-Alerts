<?php
  /**
   * create db for user info
   * @var  SQLite3 database
   */
  $db = new SQLite3('bitcoin.db');

  /**
   * SQLite3 command to create table
   * @var string
   */
  $createTextTable = "CREATE TABLE IF NOT EXISTS textUsers (
    id INTEGER PRIMARY KEY,
    phone_number TEXT,
    alert_price REAL);";

  $createEmailTable = "CREATE TABLE IF NOT EXISTS emailUsers (
    id INTEGER PRIMARY KEY,
    email TEXT,
    alert_price REAL);";

  /**
   * insert statement when user submits phone number for service
   * @var string
   */
  if (isset($_POST['phone']) && isset($_POST['alertPrice'])) {
    // adds us area code 1 if not done already
    if ($_POST['phone'] < 10000000000) {
      $_POST['phone'] = '1' . $_POST['phone'];
    }

    $insertTextUser = "INSERT INTO textUsers (phone_number, alert_price)
      VALUES ('$_POST[phone]', '$_POST[alertPrice]');";

    $textStatement = $db->prepare($createTextTable);
    $textStatement->execute();

    $db->exec($insertTextUser);

    // text confirmation
    $nexmoRequest = "https://rest.nexmo.com/sms/json?api_key=7bdca338&api_secret=a6b575aa&";
    $nexmoRequest .= "from=12134657635&to=" . $_POST['phone'];
    $nexmoRequest .= "&text=Hello%2c+you+have+been+subscribed+to+receive+text+alerts+when+";
    $nexmoRequest .= "the+price+of+Bitcoins+drop+below+" . $_POST['alertPrice'] . "+USD";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $nexmoRequest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    curl_exec($ch);
  }

  /**
   * Email alert processing
   */
  if (isset($_POST['email']) && isset($_POST['alertPriceEmail'])) {
    $insertEmailUser = "INSERT INTO emailUsers (email, alert_price)
      VALUES ('$_POST[email]', '$_POST[alertPriceEmail]');";

    $emailStatement = $db->prepare($createEmailTable);
    $emailStatement->execute();

    $db->exec($insertEmailUser);

    // email confirmation
    $to = $_POST['email'];
    $subject = 'Bitcoin Alerter Notification';
    $message = 'Hello, you have been subscribed to receive email alerts when the price of bitcoin drops below ' . $_POST['alertPriceEmail'] . '.';
    $headers = 'From: <bitcoinalerterservice@gmail.com>';
    $message = wordwrap($message, 70);

    mail($to, $subject, $message, $headers);
  }

  // /**
  //  * SMS alert processing :(              TOOOOOOOOOO     DOOOOOOOOOOOO
  //  */
  // if (isset($_POST['phone']) && isset($_POST['alertPrice'])) {
  //   $insertTextUser = "INSERT INTO textUsers"
  // }

  header("Location: project.html");

?>
