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
    // this displays the intern name from the table
    if ($errors == 0) {
        // explodes row into associative array format
        $row = mysqli_fetch_assoc($queryResult);
        $DisplayName = $row['first'] . " " . $row['last'];  
    }
    else {
        $DisplayName = "";
    }
    echo "\$DisplayName: $DisplayName";
    $TableName = "seminars";
    
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
        //Selecting the opportunity id from the table for the user
    $selectedSeminars = array();
        $SQLstring = "SELECT opportunityID FROM $TableName" . " WHERE internID='" . $_SESSION['internID'] . "'";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        if (mysqli_num_rows($queryResult) > 0) {
            // this gets the row  of selected opportunities in the table
            while (($row = mysqli_fetch_row($queryResult)) != false) {
                $selectedSeminars[] = $row[0]; 
            }
            mysqli_free_result($queryResult);
        }
        $assignedSeminars = array();
        $SQLstring = "SELECT opportunityID FROM $TableName" . " WHERE dateApproved IS NOT NULL";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
    if (mysqli_num_rows($queryResult) > 0) {
            // this gets the row  of selected opportunities in the table
            while (($row = mysqli_fetch_row($queryResult)) != false) {
                $assignedSeminars[] = $row[0]; 
            }
            mysqli_free_result($queryResult);
        }
    $TableName = "opportunities";
    $Seminars = array();
    $SQLstring = "SELECT opportunityID, company, city," . " startDate, endDate, position, description" .
        " FROM $TableName";
    $queryResult = mysqli_query($DBConnect , $SQLstring);
        if (mysqli_num_rows($queryResult) > 0) {
            // this gets the row  of selected opportunities in the table
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
        echo "<p>You last requested to attend a seminar" . " on $lastRequestDate.</p>\n";
    }
    // makes a log out option and a border(line)
    echo "<table border='1' width='100%'>\n";
    echo "<tr>\n";
    echo "<th style='background-color: cyan'>Company</th>\n";
    echo "<th style='background-color: cyan'>City</th>\n";
    echo "<th style='background-color: cyan'>State</th>\n";
    echo "<th style='background-color: cyan'>End Date</th>\n";
    echo "<th style='background-color: cyan'>Position</th>\n";
    echo "<th style='background-color: cyan'>Description</th>\n";
    echo "<th style='background-color: cyan'>Status</th>\n";
    echo "</tr>\n";
    // this shows the content for the table
    foreach ($Seminars as $Seminar) {
        // if it is not in the assigned_opportunities array then it will create a row
        if (!in_array($opportunity['seminarID'], $assignedSeminars)) {
            echo "<tr>\n";
            echo "<td>" . htmlentities($Seminar['company']) . "</td>\n";
            echo "<td>" . htmlentities($Seminar['city']) . "</td>\n";
            echo "<td>" . htmlentities($Seminar['startDate']) . "</td>\n";
            echo "<td>" . htmlentities($Seminar['endDate']) . "</td>\n";
            echo "<td>" . htmlentities($Seminar['position']) . "</td>\n";
            echo "<td>" . htmlentities($Seminar['description']) . "</td>\n";
            echo "<td>\n";
            // if selected_opportunities is in an array then it will display an echo
            if (in_array($Seminar['seminarID'], $selectedSeminars)) {
                echo "Selected";
            }
            // if the intern is appoved for an opportunity then it will display an echo
            else if($approvedSeminars > 0) {
                echo "Open";
            }
            // makes a hyperlink for the avaliable internships in the "status" column
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