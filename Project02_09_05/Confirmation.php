<?php
session_start();
//global variables
$body = "";
$errors = 0;

if (!isset($_SESSION['userID'])) {
    ++$errors;
    $body .= "<p>You have not logged in or registered." . " Please return to the " . "<a href='InternLogin.php'>" . "Registration / Login Page</a></p>";
}

if ($errors == 0) {
if (isset($_GET['seminarID']))
    $opportunityID = $_GET['seminarID'];
}
// indicates that there was no opportunity chosen
else {
    ++$errors;
    $body .= "<p>You have not selected a seminar " . " Please return to the " . "<a href='Seminars.php?" . "PHPSESSID=" . session_id() . "'>" . "Available Seminars Page</a></p>\n";
}
 $hostname = "localhost";
    $username = "adminer";
    $passwd = "sense-grass-80";
    $DBConnect = false;
    $DBName = "conference1"; 
    // if there are no errors then it will connect
    if ($errors == 0) {
        $DBConnect = mysqli_connect($hostname, $username, $passwd);
        // if there is no connection to the server then it will display an error 
        if (!$DBConnect) {
            ++$errors;
             $body .= "<p>Unable to connect to database server error code: " . mysqli_connect_error() . "</p>\n";
        }
        else {
            // this will select the database that we want to work on
            $result = mysqli_select_db($DBConnect, $DBName);
            // if there is no result to selecting the database then it will display an error
            if (!$result) {
                ++$errors;
            $body .= "<p>Unable to select the database \"$DBName\" error code: " . mysqli_error($DBConnect) . "</p>\n";
            } 
        }
    }
// displays the date on the page
$displayDate = date("l, F j, Y, g:i A");
$body .= "\$displayDate: $displayDate<br>";
$dbDate = date("Y-m-d H:i:s");
$body .= "\$dbDate: $dbDate<br>";
// inserts new columns for the table
if ($errors == 0) {
    $tableName = "assigned_seminars";
    $SQLstring = "INSERT INTO $tableName" . " (seminarsID, userID, dateSelected)" . " VALUES($seminarID, " . $_SESSION['userID'] . ", '$dbDate')";
    $queryResult = mysqli_query($DBConnect, $SQLstring);
}
if (!$queryResult) {
    ++$errors;
    $body .= "<p>Unable to execute the query, " . "error code: " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</p>\n";
}
// shows the date of entry of results
else {
    $body .= "<p>You results for seminar # " .  " $opportunityID have been entered on" . " $displayDate.</p>\n";
}
// closes database
    if ($DBConnect) {
        $body .= "<p>Closing database \"$DBName\" connection.</p>\n";
        mysqli_close($DBConnect);
    }
if ($_SESSION['userID'] > 0) {
    $body = "<p>Return to the " . "<a href='Seminars.php?" . "PHPSESSID=" . session_id() . "'>Available Seminars" . "</a> page.</p>\n";
}
// indicates that you must login to website in order to be on the opportunities page
else {
$body .= "<p>Please " . "<a> href='index.php'>" . "Register" . "</a> to use this page.</p>\n";
}
// sets the cookie for the request date
if ($errors == 0) {
    echo "Setting cookie<br>";
    setcookie("LastRequestDate", urldecode($displayDate), time()+60*60*24*7);
}
?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Seminar</title>
    <script src="modernizr.custom.65897.js"></script>
</head>

<body>
    <h2>Request Seminar</h2>
    <?php
    echo $body;
    $errors = 0;
   
    ?>
</body>
</html>
