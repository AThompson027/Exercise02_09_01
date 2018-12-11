<?php
session_start();

//global variables
$body = "";
$errors = 0;
$email = "";
$hostname = "localhost";
$username = "adminer";
$passwd = "sense-grass-80";
$DBConnect = false;
$DBName = "conference1";    

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
        $SQLstring = "UPDATE users SET first = '$first2', last = '$last2', company = '$company2', email = '$email2'";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        // if the user inputs an email that is already in the server then it will provide an error indicating so
    }
    if ($errors == 0) {
        $first2 = stripslashes($_POST['first2']);
        $last2 = stripslashes($_POST['last2']);
        $company2 = stripslashes($_POST['company2']);
        $email2 = $_POST['email2'];
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

if ($DBConnect) {
        $body .= "<p>Closing database \"$DBName\" connection.</p>\n";
        mysqli_close($DBConnect);
    }
?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Page</title>
</head>

<body>
    <form action="DisplayInfo.php?PHPSESSID=<?php echo session_id();?>" method="post">
        <p>
            Enter your name: First:
            <input type="text" name="first2">
            Last:
            <input type="text" name="last2">
            <br>
            Enter your company name:
            <input type="text" name="company2">
        </p>
        <p>
            Enter your email address:
            <input type="text" name="email2">
        </p>
        <p>
            <br>
            <input type="reset" name="reset" value="Reset Registration Form">
            <input type="submit" name="register" value="Register">
        </p>
    </form>
</body>
</html>
