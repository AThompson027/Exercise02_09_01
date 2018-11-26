<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Opportunities</title>
    <script src="modernizr.custom.65897.js"></script>
</head>

<body>
    <h1>College Internship</h1>
    <h2>Available Opportunities</h2>
    <?php
    // the request let's anyone no matter what they usedto get to this file to get and internID
    if (isset($_REQUEST['internID'])) {
        $internID = $_REQUEST['internID'];
    } else {
        $internID = -1;
    }
    //debug
    echo "\$internID: $internID\n";
    
    $errors = 0;
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
    $TableName = "interns";
    if ($errors == 0) {
        $SQLstring = "SELECT * FROM $TableName" . " WHERE internID='$internID'";
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
            echo "<p>Invalid Intern ID!</p>\n";
        }
    }
    }
    // this displays the intern name from the table
    if ($errors == 0) {
        // explodes row into associative array format
        $row = mysqli_fetch_assoc($queryResult);
        $internName = $row['first'] . " " . $row['last'];  
    }
    else {
        $internName = "";
    }
    echo "\$internName: $internName";
    $TableName = "assigned_opportunities";
    
    if ($errors == 0) {
    //counts the ID from the table
    $SQLstring = "SELECT COUNT(opportunityID)" . " FROM $TableName" . " WHERE internID='$internID'" . " AND dateApproved IS NOT NULL";
    $queryResult = mysqli_query($DBConnect, $SQLstring);
        if (mysqli_num_rows($queryResult) > 0) {
         // explodes row into array format
        $row = mysqli_fetch_row($queryResult);
        $approvedOpportunities = $row[0];
        mysqli_free_result($queryResult);
    }
        //Selecting the opportunity id from the table for the user
    $selectedOpportunities = array();
        $SQLstring = "SELECT opportunityID FROM $TableName" . " WHERE internID='$internID'";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        if (mysqli_num_rows($queryResult) > 0) {
            // this gets the row  of selected opportunities in the table
            while (($row = mysqli_fetch_row($queryResult)) != false) {
                $selectedOpportunities[] = $row[0]; 
            }
            mysqli_free_result($queryResult);
        }
        $assignedOpportunities = array();
        $SQLstring = "SELECT opportunityID FROM $TableName" . " WHERE dateApproved IS NOT NULL";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
    if (mysqli_num_rows($queryResult) > 0) {
            // this gets the row  of selected opportunities in the table
            while (($row = mysqli_fetch_row($queryResult)) != false) {
                $assignedOpportunities[] = $row[0]; 
            }
            mysqli_free_result($queryResult);
        }
    $TableName = "opportunities";
    $opportunities = array();
    $SQLstring = "SELECT opportunityID, company, city," . " startDate, endDate, position, description" .
        " FROM $TableName";
    $queryResult = mysqli_query($DBConnect , $SQLstring);
        if (mysqli_num_rows($queryResult) > 0) {
            // this gets the row  of selected opportunities in the table
            while (($row = mysqli_fetch_assoc($queryResult)) != false) {
                $opportunities[] = $row; 
            }
            mysqli_free_result($queryResult);
        } 
    }
        // closes database
    if ($DBConnect) {
        echo "<p>Closing database \"$DBName\" connection.</p>\n";
            mysqli_close($DBConnect);
    }
    // makes a log out option and a border(line)
    echo "<table border='1' width='100%'>\n";
    echo "</table>\n";
    echo "<p><a href='internLogin.php'>Log Out</a></p>\n";
    ?>
</body>
</html>
