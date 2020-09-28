#!/usr/local/bin/php
<?php
  //specify session save path
  session_save_path(dirname(realpath(__FILE__)) . '/sessions/');
  //resume session
  session_name('AccessingWebsite');
  //start the session
  session_start();

  //get user's email from the link we wrote and provided user in email
  $user_email = $_GET['email'];
  //get the password from the link we wrote and provided user in email
  $hashed_pass = $_GET['token'];
  //And combine the email and the hashed password into a string separated by tab
  $user_account = $user_email . "\t" . $hashed_pass;

  //we open the file containing unvalidated accounts in reading mode
  $file_unvalidated = fopen('unvalidated.txt', 'r') or die('cannot open file');
  //create an empty array tha will store all lines in file
  $file_contents = array();
  //while not the end of file
  while (!feof($file_unvalidated)) {
    //Read and extract one line of the file
    $line = fgets($file_unvalidated);
    //Strip whitspace from the beginning and ending of the line in file
    $line = trim($line);
    //if the line is not the same as the one provided in the link
    if ($line !== $user_account) {
      //we add it into the array
      array_push($file_contents, $line);
    }
  }
  //close the file of unvalidated accounts
  fclose($file_unvalidated);

  //open the file of unvalidated accounts in writing mode
  $file_unvalidated = fopen('unvalidated.txt', 'w') or die('cannot open file');
  //for each line store in the file array
  for ($i = 0; $i < count($file_contents); ++$i) {
    //we overwrite the file with new contents that exclude line with the same name and password in link
    fwrite($file_unvalidated, $file_contents[$i] . "\n");
  }
  //close the file
  fclose($file_unvalidated);

  //if the file of existing users does not exist
  if (!file_exists('existing_users.txt')) {
    //we create the file of validated accounts in writing mode
    $file_validated = fopen('existing_users.txt', 'w') or die('cannot create file');
    //write the user account into the validated file
    fwrite($file_validated, $user_account . "\n");
    //close the file
    fclose($file_validated);
  }
  //otherwise if the file exist
  else {
    //we open the file of validated accounts in reading mode
    $file_validated = fopen('existing_users.txt', 'r') or die('cannot open file');
    //let repeated accounts in file be false by default
    $repeated_account = false;
    //while not the end of file
    while (!feof($file_validated)) {
      //Read and extract one line of the file
      $line = fgets($file_validated);
      //Strip whitspace from the beginning and ending of the line in file
      $line = trim($line);
      //if a line matches what the user has provided
      if ($line === $user_account) {
        //there is a repeated account
        $repeated_account = true;
        break;
      }
    }
    //close the file
    fclose($file_validated);

    //open the file of validated accounts in append mode
    $file_validated = fopen('existing_users.txt', 'a');
    //if there is no repeated account
    if (!$repeated_account) {
      //we include the user email and password into the file
      fwrite($file_validated, $user_account . "\n");
    }
    //close the file
    fclose($file_validated);
  }
?>
<!DOCTYPE html>
<html>
<head>
  <title>Validation Page</title>
</head>
<body>
  <p>
    You are registered!
  </p>
</body>
</html>
