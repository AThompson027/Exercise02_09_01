<?php
session_start();

echo "Session id: " . session_id() . "<br>\n";
?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Seminars</title>
    <script src="modernizr.custom.65897.js"></script>
</head>

<body>
    <h2>Available Seminars</h2>
        <p>Choose a seminar you would like to attend!</p>
    <?php
    if (isset($_COOKIE['LastRequestDate'])) {
        $lastRequestDate = $_COOKIE['LastRequestDate'];
    } else {
        $lastRequestDate = "";
    }
    $errors = 0;
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
             echo "<p>Unable to connect to database server error code: " . mysqli_connect_error() . "</p>\n";
        }
        else {
            // this will select the database that we want to work on
            $result = mysqli_select_db($DBConnect, $DBName);
            // if there is no result to selecting the database then it will display an error
            if (!$result) {
                ++$errors;
            echo "<p>Unable to select the database \"$DBName\" error code: " . mysqli_error($DBConnect) . "</p>\n";
            } 
        }
    }
    // This selects the ID from the table
    $TableName = "users";
    if ($errors == 0) {
        $SQLstring = "SELECT * FROM $TableName" . " WHERE userID='" . $_SESSION['userID'] . "'";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        // if there is no result then there will be an error
        if (!$queryResult) {
            ++$errors;
            echo "<p>Unable to execute the query, error code: " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</p>\n";
        } 
        // this indicated that the ID might be incorrect
        else {
        if (mysqli_num_rows($queryResult) == 0) {
            ++$errors;
            echo "<p>Invalid User ID!</p>\n";
        }
    }
    }
    // this displays the user name from the table
    if ($errors == 0) {
        // explodes row into associative array format
        $row = mysqli_fetch_assoc($queryResult);
        $fullName = $row['first'] . " " . $row['last'];  
    }
    else {
        $fullName = "";
    }
    echo "\$fullName: $fullName";
    $TableName = "assigned_seminars";
    
    if ($errors == 0) {
    //counts the ID from the table
    $SQLstring = "SELECT COUNT(seminarID)" . " FROM $TableName" . " WHERE userID='" . $_SESSION['userID'] . "'" . " AND dateApproved IS NOT NULL";
    $queryResult = mysqli_query($DBConnect, $SQLstring);
        if (mysqli_num_rows($queryResult) > 0) {
         // explodes row into array format
        $row = mysqli_fetch_row($queryResult);
        $approvedSeminars = $row[0];
        mysqli_free_result($queryResult);
    }
        //Selecting the seminar id from the table for the user
        $selectedSeminars = array();
        $SQLstring = "SELECT seminarID FROM $TableName" . " WHERE userID='" . $_SESSION['userID'] . "'";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        if (mysqli_num_rows($queryResult) > 0) {
            // this gets the row of selected seminars in the table
            while (($row = mysqli_fetch_row($queryResult)) != false) {
                $selectedSeminars[] = $row[0]; 
            }
            mysqli_free_result($queryResult);
        }
        $assignedSeminars = array();
        $SQLstring = "SELECT seminarID FROM $TableName" . " WHERE dateApproved IS NOT NULL";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
    if (mysqli_num_rows($queryResult) > 0) {
            // this gets the row  of selected seminars in the table
            while (($row = mysqli_fetch_row($queryResult)) != false) {
                $assignedSeminars[] = $row[0]; 
            }
            mysqli_free_result($queryResult);
        }
    $TableName = "seminars";
    $Seminars = array();
    $SQLstring = "SELECT seminarID, seminar, city," . " startDate, endDate, topic, description" .
        " FROM $TableName";
    $queryResult = mysqli_query($DBConnect , $SQLstring);
        if (mysqli_num_rows($queryResult) > 0) {
            // this gets the row  of selected seminars in the table
            while (($row = mysqli_fetch_assoc($queryResult)) != false) {
                $Seminars[] = $row; 
            }
            mysqli_free_result($queryResult);
        } 
    }
        // closes database
    if ($DBConnect) {
        echo "<p>Closing database \"$DBName\" connection.</p>\n";
            mysqli_close($DBConnect);
    }
    if (!empty($lastRequestDate)) {
        echo "<p>You last requested a seminar" . " on $lastRequestDate.</p>\n";
    }
    echo "<table border='1' width='100%'>\n";
    echo "<tr>\n";
    echo "<th style='background-color: lightgray'>Seminar</th>\n";
    echo "<th style='background-color: lightgray'>City</th>\n";
    echo "<th style='background-color: lightgray'>Start Date</th>\n";
    echo "<th style='background-color: lightgray'>End Date</th>\n";
    echo "<th style='background-color: lightgray'>Topic</th>\n";
    echo "<th style='background-color: lightgray'>Description</th>\n";
    echo "<th style='background-color: lightgray'>Status</th>\n";
    echo "</tr>\n";
    // this shows the content for the table
    foreach ($Seminars as $Seminar) {
        // if it is not in the assignedSeminars array then it will create a row
        if (!in_array($Seminar['seminarID'], $assignedSeminars)) {
            echo "<tr>\n";
            echo "<td>" . htmlentities($Seminar['seminar']) . "</td>\n";
            echo "<td>" . htmlentities($Seminar['city']) . "</td>\n";
            echo "<td>" . htmlentities($Seminar['startDate']) . "</td>\n";
            echo "<td>" . htmlentities($Seminar['endDate']) . "</td>\n";
            echo "<td>" . htmlentities($Seminar['topic']) . "</td>\n";
            echo "<td>" . htmlentities($Seminar['description']) . "</td>\n";
            echo "<td>\n";
            // if selectedSeminars is in an array then it will display an echo
            if (in_array($Seminar['seminarID'], $selectedSeminars)) {
                echo "Selected";
            }
//             if the user is approved for a seminar then it will display an echo
            else if($approvedSeminars > 0) {
                echo "Open";
            }
            // makes a hyperlink for the avaliable seminars in the "status" column
            else {
                echo "<a href='DisplayInfo.php?" . "PHPSESSID=" . session_id() . "&seminarID=" . $Seminar['seminarID'] . "'>Available</a>\n";
            }
            echo "</td>\n";
            echo "</tr>\n";
        }
    }
    echo "</table>\n";
    ?>
</body>
</html>
