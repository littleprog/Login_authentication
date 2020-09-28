#!/usr/local/bin/php
<?php
  //specify session save path
  session_save_path(dirname(realpath(__FILE__)) . '/sessions/');
  //resume session
  session_name("AccessingWebsite");
  //start the session
  session_start();
  ?>
  <!DOCTYPE html>
   <?php
   //if the user did not login the intended way, provide him with a different page
  if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) { ?>
  <html lang = "en">
    <head>
      <title>Unwelcome</title>
    </head>
    <body>
      <p>Go back and log in.</p>
    </body>
  </html> <?php }

  //Otherwise if user login through the login page, welcome him and show his email address
  else { ?>
  <html lang = "en">
  <head>
    <meta charset = "utf-8" />
    <title>Welcome Page</title>
  </head>
  <body>
    <main>
      <p>
        <?php
        //welcome him and show his email address
        echo 'Welcome. Your email address is ', $_GET['email'];
        ?>
      </p>
      <p>
        <?php
        //print out a message
          echo 'Here is a list of all registered addresses: ';
          //open the file of validated users in reading mode
          $file_validated = fopen('existing_users.txt', 'r') or die('cannot open file');
          //while not end of file, we will be printing out all users with validated accounts
          while (!feof($file_validated )) {
            //Read and extract one line of the file
            $line = fgets($file_validated);
            //Strip whitspace from the beginning and ending of the line in file
            $line = trim($line);
            //Break up the line by the tab character and store the contents of line in an array
            $email_password_arr = explode("\t",$line);
            //user email is the first element of array
            $user_email = $email_password_arr[0];
            //print out user email
            echo $user_email, ' ';
          }
          //close the file
          fclose($file_validated);
          ?>
      </p>
      <p>
        <a href= "https://www.sengchowchoy.com/logout.php"><button type="button">log out</button></a>
      </p>
    </main>
  </body>
</html> <?php }
?>
