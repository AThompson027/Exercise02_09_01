<?php
session_start();
//global variables
$body = "";
$errors = 0;
//$internID = 0;
// we use a GET because we used a hyperlink (URL) to get to this file
//if (isset($_GET['internID'])) {
//    $internID = $_GET['internID'];
//} 
if (!isset($_SESSION['internID'])) {
    ++$errors;
    $body .= "<p>You have not logged in or registered." . " Please return to the " . "<a href='InternLogin.php'>" . "Registration / Login Page</a></p>";
}

if ($errors == 0) {
if (isset($_GET['opportunityID']))
    $opportunityID = $_GET['opportunityID'];
}
// indicates that there was no opportunity chosen
else {
    ++$errors;
    $body .= "<p>You have not selected an opportunity " . " Please return to the " . "<a href='AvailableOpportunities.php?" . "PHPSESSID=" . session_id() . "'>" . "Available Opportunities Page</a></p>\n";
}
 $hostname = "localhost";
    $username = "adminer";
    $passwd = "sense-grass-80";
    $DBConnect = false;
    $DBName = "interships1"; 
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
    $tableName = "assigned_opportunities";
    $SQLstring = "INSERT INTO $tableName" . " (opportunityID, internID, dateSelected)" . " VALUES($opportunityID, " . $_SESSION['internID'] . ", '$dbDate')";
    $queryResult = mysqli_query($DBConnect, $SQLstring);
}
if (!$queryResult) {
    ++$errors;
    $body .= "<p>Unable to execute the query, " . "error code: " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</p>\n";
}
// shows the date of entry of results
else {
    $body .= "<p>You results for opportunity # " .  " $opportunityID have been entered on" . " $displayDate.</p>\n";
}
// closes database
    if ($DBConnect) {
        $body .= "<p>Closing database \"$DBName\" connection.</p>\n";
        mysqli_close($DBConnect);
    }
if ($_SESSION['internID'] > 0) {
    $body = "<p>Return to the " . "<a href='AvailableOpportunities.php?" . "PHPSESSID=" . session_id() . "'>Available Opportunities" . "</a> page.</p>\n";
}
// indicates that you must login to website in order to be on the opportunities page
else {
$body .= "<p>Please " . "<a> href='InternLogin.php'>" . "Register or Log In" . "</a> to use this page.</p>\n";
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
    <title>Request Opportunity</title>
    <script src="modernizr.custom.65897.js"></script>
</head>

<body>
    <h1>College Internship</h1>
    <h2>Opportunity Requested</h2>
    <?php
    echo $body;
    $errors = 0;
   
    ?>
</body>
</html>
