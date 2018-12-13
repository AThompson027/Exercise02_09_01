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
    
    
    //set the varibles
    if ($errors == 0) {
        $first2 = stripslashes($_POST['first2']);
        $last2 = stripslashes($_POST['last2']);
        $company2 = stripslashes($_POST['company2']);
        $email2 = $_POST['email2'];
//        $queryResult = mysqli_query($DBConnect, $SQLstring);
        // if there are no results to making the table then it will give an error
//        if (!$queryResult) {
//            ++$errors;
//            $body .= "<p>Unable to register information error code: " . mysqli_error($DBConnect) . "</p>\n";
//        }
    }

    
    // if there are no errors
    $TableName = "users";
    if ($errors == 0) {
        $SQLstring = "UPDATE users" . "SET first = $first2, last = $last2, company = $company2, email = $email2" . " WHERE userID=" . $_SESSION['userID'];
        $queryResult = mysqli_query($DBConnect, $SQLstring);
    }
    
    }

if ($DBConnect) {
        $body .= "<p>Closing database \"$DBName\" connection.</p>\n";
        mysqli_close($DBConnect);
    }

    echo"<p><a href='DisplayInfo.php?" . "PHPSESSID=" . session_id() . "'>Return to Information" . "</a></p>\n";

?>