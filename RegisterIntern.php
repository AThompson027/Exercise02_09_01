<?php
//global variables
$body = "";
$errors = 0;
$email = "";
$hostname = "localhost";
$username = "adminer";
$passwd = "sense-grass-80";
$DBConnect = false;
$DBName = "interships1";    

// if the email field is empty then it will increment the errors and echo out a paragraph stating an error message
if (empty($_POST['email'])) {
    ++$errors;
    $body .= "<p>You need to enter an e-mail address.</p>\n";
}
else {
    $email = stripslashes($_POST['email']);
        // this regex will validate the email address input
    if (preg_match("/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[w-]+)*(\.[A-Za-z]{2,})$/i", $email) == 0) {
        ++$errors;
        $body .= "<p>You need to enter a valid e-mail address.</p>\n";
        $email = "";
    }
}
    // if the password field is empty
if (empty($_POST['password'])) {
    ++$errors;
    $body .= "<p>You need to enter password.</p>\n";
}
else {
    $password = stripslashes($_POST['password']);   
}
    // if the confirmation is empty then it will have an error
if (empty($_POST['password2'])) {
    ++$errors;
    $body .= "<p>You need to enter confirmation password.</p>\n";
}
else {
    $password2 = stripslashes($_POST['password2']);   
}
if (!empty($password) && !empty($password2)) {
    // checks string length of the passwords for less than 6
    if (strlen($password) < 6){
        $body .= "<p>The password is too short.</p>\n";
        $password = "";
        $password2 = "";
    }
    // if the confirmation and the password do not match
    if ($password <> $password2) {
        ++$errors;
        $body .= "<p>The passwords do not match</p>\n";
        $password = "";
        $password2 = "";
    }
}
 // if there are no errors then it will connect to the database
if ($errors == 0) {
    $DBConnect = mysqli_connect($hostname, $username, $passwd);
    //if we cannot connect to the database then it will display an error
    if(!$DBConnect) {
        ++$errors;
        $body .= "<p>Unable to connect to database server error code: " . mysqli_connect_error() . "</p>\n";
    }
    // if there is no result to select the database then there will be an error
    else {
        $result = mysqli_select_db($DBConnect, $DBName);
        if (!$result) {
            ++$errors;
            $body .= "<p>Unable to select the database \"$DBName\" error code: " . mysqli_error($DBConnect) . "</p>\n";
        }
    }
    // if there are no errors
    $TableName = "interns";
    // counts how many rows are in the email field in the table
    if ($errors == 0) {
        $SQLstring = "SELECT count(*) FROM $TableName" . 
            " WHERE email='$email'";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        // if the user inputs an email that is already in the server then it will provide an error indicating so
        if ($queryResult) {
            $row = mysqli_fetch_row($queryResult);
            if ($row[0] > 0){
                ++$errors;
                $body .= "<p>The e-mail address entered (" . htmlentities($email) . ") is already registered.</p>\n";
            }
        }
    }
    if ($errors == 0) {
        $first = stripslashes($_POST['first']);
        $last = stripslashes($_POST['last']);
        $SQLstring = "INSERT INTO $TableName" . " (first, last, email, password_md5)" . " VALUES('$first', '$last', '$email', " . "'" . md5($password) . "')";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        // if there are no results to making the table then it will give an error
        if (!$queryResult) {
            ++$errors;
            $body .= "<p>Unable to your registration information error code: " . mysqli_error($DBConnect) . "</p>\n";
        }
        else {
//            $internID = mysqli_insert_id($DBConnect);
            $_SESSION['internID'] = mysqli_insert_id($DBConnect);
        }
    }

    }
    // gives user their id
    if ($errors == 0) {
        $internName = $first . "" . $last;
        $body .= "<p>Thank you, $internName. ";
        $body .= "Your new intern ID is <strong>" . $_SESSION['internID'] . "</strong></p>\n";
    }
    // closes database
    if ($DBConnect) {
        // this makes a cookie
      //  setcookie("internID", $internID);
        setcookie("internID", $_SESSION['internID']);
        // if there is no errors then the database will disconnect.
        $body .= "<p>Closing database \"$DBName\" connection.</p>\n";
        $body .= "<p><a href='AvailableOpportunities.php?" . "PHPSESSID=" . session_id() . "'>" . "View Available Opportunities</a></p>\n";
        mysqli_close($DBConnect);
//        $body .= "<form action='AvailableOpportunities.php' method='post'>\n";
//        $body .= "<input type ='hidden' name='internID' value='$internID'>\n";
//        $body .= "<input type='submit' name='submit' value='View Available Opportunities'>\n";
//        $body .= "</form>\n";
    }
    // this indicates to correct their errors
    if ($errors > 0) {
         $body .= "Please use your browser's BACK button to return to the form and fix the errors indicated.";
     }
?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Registration</title>
</head>
<h1>College Internship</h1>
<h2>Intern Registration</h2>
<body>
<?php
echo "<pre>\n";
print_r($_COOKIE);
echo "</pre>\n>";
    echo $body;
    ?>
</body>
</html>
