<?php
session_start();
//global variables
$body = "";
$errors = 0;
if (!isset($_SESSION['userID'])) {
    ++$errors;
    $body .= "<p>You have not logged in or registered." . " Please return to the " . "<a href='index.php'>" . "Registration / Login Page</a></p>";
}

if ($errors == 0) {
if (isset($_GET['seminarID'])) {
    $seminarID = $_GET['seminarID'];
    }
}
// indicates that there was no seminar chosen
else {
    ++$errors;
    $body .= "<p>You have not selected a seminar " . " Please return to the " . "<a href='Seminars.php?" . "PHPSESSID=" . session_id() . "'>" . "Available Seminars Page</a></p>\n";
}
//global variables
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
$dbDate = date("Y-m-d H:i:s");

$SQLstring = "SELECT * FROM users" . " WHERE userID='" . $_SESSION['userID'] . "'";
    $queryResult = mysqli_query($DBConnect, $SQLstring);

// inserts new columns for the table
if ($errors == 0) {
    $tableName = "assigned_seminars";
    $SQLstring = "INSERT INTO $tableName" . " (seminarID, userID, dateSelected)" . " VALUES($seminarID, " . $_SESSION['userID'] . ", '$dbDate')";
}

if ($errors == 0) {
        // explodes row into associative array format
        $row = mysqli_fetch_assoc($queryResult);
        $fullName = $row['first'] . " " . $row['last'];
        $company = $row['company'];
        $email = $row['email'];
    }
    else {
        $fullName = "";
        $company = "";
        $email = "";
    }

if (isset($_POST['Enter'])) {
    $row = mysqli_fetch_assoc($queryResult);
    $fullName = $row['first2'] . " " . $row['last2'];
    $company = $row['company2'];
    $email = $row['email2'];
}


if (!$queryResult) {
    ++$errors;
    $body .= "<p>Unable to execute the query, " . "error code: " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</p>\n";
}
// shows the date of entry of results
else {
    $body .= "<p>Your results for seminar # " .  " $seminarID have been entered on" . " $displayDate.</p>\n";
}
// closes database
    if ($DBConnect) {
        $body .= "<p>Closing database \"$DBName\" connection.</p>\n";
        mysqli_close($DBConnect);
    }
// sets the cookie for the request date
if ($errors == 0) {
    setcookie("LastRequestDate", urldecode($displayDate), time()+60*60*24*7);
}
?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requested Seminar</title>
    <script src="modernizr.custom.65897.js"></script>
</head>

<body>
    <h2>Seminar Requested</h2>
    <?php
    echo $body;
    $errors = 0;
    echo "<form action='Confirmation.php?PHPSESSID=<?php echo session_id();?>' method='post'>";
        echo "<h2>Is this information correct?</h2>";
        echo "Name: $fullName<br>";
        echo "Company: $company<br>";
        echo "Email: $email<br>";
    echo "<input type='submit' name='yes' value='Yes'>";
    echo "</form>";
    
    echo "<form action='Edit.php?PHPSESSID=<?php echo session_id();?>' method='post'>";
    echo "<input type='submit' name='edit' value='Edit'>";
    echo "</form>";
    ?>
</body>
</html>