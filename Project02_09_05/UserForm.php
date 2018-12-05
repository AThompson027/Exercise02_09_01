<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Registration</title>
</head>
<h2>Conference Registration</h2>

<body>
    <?php
//global variables
$body = "";
$errors = 0;
$email = "";
$hostname = "localhost";
$username = "adminer";
$passwd = "sense-grass-80";
$DBConnect = false;
$DBName = "conference1";    

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
    $TableName = "users";
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
        $company = stripslashes($_POST['company']);
        $SQLstring = "INSERT INTO $TableName" . " (first, last, email, company)" . " VALUES('$first', '$last', '$email', '$company')";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        // if there are no results to making the table then it will give an error
        if (!$queryResult) {
            ++$errors;
            $body .= "<p>Unable to register information error code: " . mysqli_error($DBConnect) . "</p>\n";
        }
        else {
            $_SESSION['userID'] = mysqli_insert_id($DBConnect);
        }
    }

    }
    // gives user their id
    if ($errors == 0) {
        $UserName = $first . "" . $last;
        $body .= "<p>Thank you, $UserName. ";
        $body .= "Your new user ID is <strong>" . $_SESSION['userID'] . "</strong></p>\n";
    }
    // closes database
    if ($DBConnect) {
        // this makes a cookie
        setcookie("userID", $_SESSION['userID']);
        // if there is no errors then the database will disconnect.
        $body .= "<p>Closing database \"$DBName\" connection.</p>\n";
        $body .= "<p><a href='Seminars.php?" . "PHPSESSID=" . session_id() . "'>" . "Seminars</a></p>\n";
        mysqli_close($DBConnect);
    }
    // this indicates to correct their errors
    if ($errors > 0) {
         $body .= "Please use your browser's BACK button to return to the form and fix the errors indicated.";
     }
        echo $body;
?>
</body>

</html>
