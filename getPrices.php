<?php
  /**
   * script to run daily to check bitcoin prices and alert users
   * whose alert price is lower than the daily value of BTC at the time checked
   */

  // TRUE is debug mode, FALSE is live!
  // disable checking Bitcoin API when debugging
  $mutePriceFetching = FALSE;  // price is set to 1000.00 when debuggin
  // disable sending text alerts when debugging
  $muteTexting = FALSE;
  // disable sending email alerts when debugging
  $muteEmailing = FALSE;

  /**
   * Fetch Bitcoin prices
   */
  if ($mutePriceFetching == FALSE) {
    $bitcoinPriceURL = "http://api.bitcoincharts.com/v1/weighted_prices.json";

    // curl the json data for bitcoin prices
    $ch = curl_init();
    // set URL to fetch
    curl_setopt($ch, CURLOPT_URL, $bitcoinPriceURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $response = json_decode(curl_exec($ch), TRUE);

    $currentPrice = $response['USD']['24h'];
  }

  $currentDate = date("Y-m-d");
  if (!isset($currentPrice)) {
    $currentPrice = 1000.00;
  }

  echo "Current price of bitcoin in USD is: ";
  echo $currentPrice;
  echo '</br>';
  echo "Current date: ";
  echo $currentDate;
  echo '</br>';

  if ($mutePriceFetching) {
    echo "checking bitcoin API is disabled, price set to 1000.00 for debugging";
    echo '</br>';
  }
  if ($muteTexting) {
    echo "texting alerts is disabled for debugging";
    echo '</br>';
  }
  if ($muteEmailing) {
    echo "emailing alerts is disabled for debugging";
    echo '</br>';
  }

// have display if pulling real data or debugging based on mutes

  $db = new SQLite3('bitcoin.db');

  /**
   * text alerts to users
   */
  $nexmoRequestPre = "https://rest.nexmo.com/sms/json?api_key=7bdca338&api_secret=a6b575aa&from=12134657635&to=";
  $nexmoRequestPost = "&text=The+current+price+of+one+Bitcoin+is+";
  $nexmoRequestPost .= $currentPrice . "+USD";

  // prepare sqlite3 statement to select phone numbers to alert
  $selectEmailAlertees = "SELECT phone_number FROM textUsers WHERE alert_price > '$currentPrice';";
  $textStatement = $db->prepare($selectEmailAlertees);
  $textResult = $textStatement->execute();

  echo '</br>';
  echo "phones texted: ";

  /**
   * loop through each result to be texted and send text
   * $row is each phone number
   */
  $ch = curl_init();
  while ( $textRow = $textResult->fetchArray(  ) ) {
    // print the array in JSON format
    // send txt
    echo '</br>';
    echo $textRow['phone_number'];

    if (!$muteTexting) {
      $nexmoRequest = $nexmoRequestPre . $textRow['phone_number'] . $nexmoRequestPost;
      curl_setopt($ch, CURLOPT_URL, $nexmoRequest);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_exec($ch);
    }
  }

  /**
   * email alerts to users
   */

  // prepare sqlite3 statement to select phone numbers to alert
  $selectEmailAlertees = "SELECT email FROM emailUsers WHERE alert_price > '$currentPrice';";
  $emailStatement = $db->prepare($selectEmailAlertees);
  $emailResult = $emailStatement->execute();

  // prepare email alert message for user
  $subject = 'Bitcoin Alerter Notification';
  $message = 'The current price of one Bitcoin is ' . $currentPrice . 'USD.';
  $headers = 'From: <bitcoinalerterservice@gmail.com>';
  $message = wordwrap($message, 70);

  echo '</br>';
  echo "addresses emailed: ";

  while ( $emailRow = $emailResult->fetchArray(  ) ) {
    // print the array in JSON format
    // send email
    echo '</br>';
    echo $emailRow['email'];
    $to = $emailRow['email'];

    mail($to, $subject, $message, $headers);
  }
?>

