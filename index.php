#!/usr/local/bin/php
<?php
//Turn on output buffering
  ob_start();
  //specify session save path
  session_save_path(dirname(realpath(__FILE__)) . '/sessions/');
  //name the session
  session_name('AccessingWebsite');
  //start the session
  session_start();
  //Person has not log in
  $_SESSION['loggedin'] = false;

/**
This function executes when the user clicks the register button. It takes in
user inputs for email and password and determine if there is an registration error.
If there is, it will change the registration error to true. Otherwise, registration error
will be false

@param  string $email the email user input
@param string $password the password the user input
@param boolean $register_error whether there is error when user registers
*/
  function validate_register($email, $password, &$register_error) {
    //Strip whitspace from the beginning and ending of user input email and password
    $user_email = trim($email);
    $user_pass = trim($password);

    //hash the password
    $hashed_pass = hash('md2', $user_pass);
    //And combine the email and the hashed password into a string separated by tab
    $user_account = $user_email . "\t" . $hashed_pass;

    //if the file containing unvalidated accounts does not exist
    if (!file_exists('unvalidated.txt')) {
      //there is no error so do nothing
    }
    //otherwise if the file exist
    else {
      //open the file containing unvalidated account for reading
      $file_unvalidated = fopen('unvalidated.txt', 'r') or die('cannot open file');
      //by default, we have not found account so let the variable $found_account be false
      $found_account = false;
      //while end of file not reached
      while(!feof($file_unvalidated)) {
        //Read and extract one line of the file
        $line = fgets($file_unvalidated);
        //Strip whitspace from the beginning and ending of the line in file
        $line = trim($line);
        //Break up the line by the tab character and store the contents of line in an array
        $email_password_arr = explode("\t", $line);

        //if the first element of the array matches the email input by user but the
        //the second element of the array does not match the password input by the user
        if ($email_password_arr[0] === $user_email && $email_password_arr[1] !== $hashed_pass) {
          //we have found the account
          $found_account = true;
          break;
        }

        //if the contents of the line matches the string containing user email and password
        if ($user_account === $line) {
          //we have found the account
          $found_account = true;
          break;
        }
      }
      //close the file containing unvalidated accounts
      fclose($file_unvalidated);

      //if we did not find the account in the unvalidated file
      if ($found_account === false) {
        //we check to see if the file containing validated users exist
        if (!file_exists('existing_users.txt')) {
          //if there is no such file, there will be no such account so no register error
        }
        //otherwise if the file exist
        else {
          //we open the file containing validated accounts
          $file_validated = fopen('existing_users.txt', 'r') or die('cannot open file');
          //while end of file not reached
          while(!feof($file_validated)) {
            //Read and extract one line of the file
            $line = fgets($file_validated);
            //Strip whitspace from the beginning and ending of the line in file
            $line = trim($line);
            //Break up the line by the tab character and store the contents of line in an array
            $email_password_arr = explode("\t", $line);

            //if the first element of the array matches the email input by user but the
            //the second element of the array does not match the password input by the user
            if ($email_password_arr[0] === $user_email && $email_password_arr[1] !== $hashed_pass) {
              //we have found the account
              $found_account = true;
              break;
            }

            //if the contents of the line matches the string containing user email and password
            if ($user_account === $line) {
              //we have found the account
              $found_account = true;
              break;
            }
          }
          //close the file containing validated accounts
          fclose($file_validated);

          //if we find the account among validated account, it means user already registered
          if ($found_account === true) {
            //so he should not try to register again so there is registration error
            $register_error = true;
          }
        }
      }
      //Otherwise if we find his account in the file with unvalidated accounts
      else {
        //It means he has not validated yet
        $register_error = true;
      }
    }
  }

  //we assume there is no registration error in the beginning
  $register_error = false;
  //when the user clicks on the register button
  if (isset($_POST['register'])) {
    //program will begin the process of checking if user can register with the provided information
    validate_register($_POST['email'], $_POST['password'], $register_error);
  }


  /**
  This function executes when the user clicks the login button. It takes in
  user inputs for email and password and determine if there is a login error.
  If there is, it will change the login error to true. Otherwise, login error
  will be false

  @param  string $email the email user input
  @param string $password the password the user input
  @param string $login_error whether there is error when user logins
  */
  function validate_login($email, $password, &$login_error) {
    //Strip whitspace from the beginning and ending of user input email and password
    $user_email = trim($email);
    $user_pass = trim($password);

    //hash the password
    $hashed_pass = hash('md2', $user_pass);
    //And combine the email and the hashed password into a string separated by tab
    $user_account = $user_email . "\t" . $hashed_pass;

    //we check to see if the file containing validated users exist
    if (!file_exists('existing_users.txt')) {
      //if the file does not exist, user has to validate or register to move his name to file of validate users
      $login_error = 'Account need validation or registration';
    }
    //if the file exists
    else {
      //we open the file in reading mode
      $file_validated = fopen('existing_users.txt', 'r') or die('cannot open file');
      //by default, we have not found account so let the variable $found_account be false
      $found_account = false;
      //while end of the file has not been reached
      while(!feof($file_validated)) {
        //Read and extract one line of the file
        $line = fgets($file_validated);
        //Strip whitspace from the beginning and ending of the line in file
        $line = trim($line);
        //Break up the line by the tab character and store the contents of line in an array
        $email_password_arr = explode("\t", $line);

        //if the first element of the array matches the email input by user but the
        //the second element of the array does not match the password input by the user
        if ($email_password_arr[0] === $user_email && $email_password_arr[1] !== $hashed_pass) {
          //we have found the account
          $found_account = true;
          //password do not match so wrong password
          $login_error = 'Wrong password';
        }

        //if the string of combined email and hashed password matches with a line in file
        if ($user_account === $line) {
          //we have also found the account
          $found_account = true;
          //login error no error so let it be default
          break;
        }

      }
      //close the file of validated accounts
      fclose($file_validated);
      //if the account is not found in the file of validated accounts
      if ($found_account === false) {
        //user needs to register or validate
        $login_error = 'Account need validation or registration';
      }
    }
  }
  //let there be no error before user clicks login by default
  $login_error = 'No error';
  //when user clicks the login button
  if (isset($_POST['login'])) {
    //program will begin the process of checking if user can login with the provided information
    validate_login($_POST['email'], $_POST['password'], $login_error);
  }


 ?>
 <!DOCTYPE html>
 <html lang = "en">
 <head>
   <meta charset = "utf-8" />
   <link rel="stylesheet" href="style.css" />
   <title>Login Page</title>
 </head>
 <body>
   <main>
     <p class = "greeting">
       Hey, good to see you again!
     </p>
     <section class = "logo-image">
       <img src = "book_logo.jpg" alt = "logo" class = "logo" />
     </section>
     <div>
       <form method = "post" action = "<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit = "return document.getElementById('emailAddress').value.length !== 0 && document.getElementById('user_password').value.length !== 0;">
         <fieldset>
           <p class = "text-field">
             <input class = "text-inputs" type="email" pattern = "[A-Za-z\d]+@[A-Za-z\d\.]+" id = "emailAddress" name = "email" placeholder = "Email"/>
           </p>
           <p class = "text-field">
             <input class = "text-inputs" type = "password" pattern = "[A-Za-z\d]{6,}" id = "user_password" name = "password" placeholder = "Password(≥6 characters letters or digits)"/>
           </p>
         </fieldset>
         <section class = "button-field">
           <input class = "button" type = "submit" name = "register" value = "Register" />
           <input class = "button" type = "submit" name = "login" value = "Log in" />
         </section>
       </form>
     </div>
   <div>
     <?php
     //if user presses the register button
     if (isset($_POST['register'])) {
       //if there is registration error, print error message
       if ($register_error) { ?>
       <p>
         Already registered. Please log in/validate.
       </p>
    <?php }
      //Otherwise if there is no errors in registration process
       else {
         //Strip whitspace from the beginning and ending of user input email and password
         $user_email = trim($_POST['email']);
         $user_pass = trim($_POST['password']);

         //hash the password
         $hashed_pass = hash('md2', $user_pass);
         //And combine the email and the hashed password into a string separated by tab
         $user_account = $user_email . "\t" . $hashed_pass . "\n";

         //if the file containing unvalidated accounts does not exist
         if (!file_exists('unvalidated.txt')) {
           //create one in writing mode
           $file_unvalidated = fopen('unvalidated.txt', 'w') or die('cannot create file');
           //write the user email and hashed password into file
           fwrite($file_unvalidated, $user_account);
           //then close the file
           fclose($file_unvalidated);
         }
         //otherwise if file exist
         else {
           //open the file in append mode
           $file_unvalidated = fopen('unvalidated.txt', 'a') or die('cannot open file');
           //write the line at the end of file
           fwrite($file_unvalidated, $user_account);
           //then close the file
           fclose($file_unvalidated);
         }

         //Create a variable that stores email content that will be sent to user via email
         $email_message = 'Validate by clicking here: ' .
         'www.sengchowchoy.com/validate.php' .
         '?email=' .
         $user_email .
         '&token=' .
         $hashed_pass;

         //mail a validation email to user
         mail($user_email, 'Validate account', $email_message);

         //then tell user to validate his account
         echo '<p>', 'A validation email has been sent to: ', $user_email,
         '. Please follow the link.', '</p>';
       }
     } ?>
     <?php
     //if the login button is clicked
     if (isset($_POST['login'])) {
       //Strip whitspace from the beginning and ending of the user input email
       $user_email = trim($_POST['email']);

       //if there is login error is wrong password, print message
       if ($login_error === 'Wrong password') { ?>
       <p>
         Your password is invalid.
       </p>
     <?php }
        //otherwise if user needs to validate or register account, print message
       elseif ($login_error === 'Account need validation or registration') { ?>
         <p>
           No such email address. Please register or validate.
         </p>
       <?php }
       //otherwise if there is no errors
        elseif ($login_error === 'No error') {
          //change loggedin property of session superglobal to be true
          $_SESSION['loggedin'] = true;
          //link of where to send user to when he clicks login with right credentials
          $welcome_page_link = 'Location: ' . 'https://www.sengchowchoy.com/welcome.php?email=' . $user_email;
          //sent user to the page
          header($welcome_page_link);

        }
     } ?>
   </div>
  </main>
 </body>
 </html>
