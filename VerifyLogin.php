<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Login</title>
    <script src="modernizr.custom.65897.js"></script>
</head>

<body>
    <h1>College Internship</h1>
    <h2>Verify Intern Login</h2>
    <?php
    // global variables
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
    // this creates the table with the client info
    $TableName = "interns";
    if ($errors == 0) {
        $SQLstring = "SELECT internID, first, last" . " FROM $TableName" . " WHERE email='" . stripslashes($_POST['email']) . "' AND password_md5='" . md5(stripslashes($_POST['password'])) . "'";
        $queryResult = mysqli_query($DBConnect, $SQLstring);
        // how many rows are in the query result and if there are no rows returned then we will increment our error and indicate an error that the password and/or username combination is not correct
        if (mysqli_num_rows($queryResult) == 0) {
            ++$errors;
            echo "<p>The email address/password combination entered is not valid.";
        }
        // this tells it to get a row from the query result (internID row and first/last name row)
        else {
            $row = mysqli_fetch_assoc($queryResult);
            $internID = $row['internID'];
            $internName = $row['first'] . " " . $row['last'];
            //Fetch rows from a result-set, then free the memory associated with the result
            mysqli_free_result($queryResult);
            echo "<p>Welcome back, $internName!</p>\n";
        }
    }
    // if there still is a connection then it will close the connection if there are no errors
    if ($DBConnect) {
        echo "<p>Closing database \"$DBName\" connection.</p>\n";
        mysqli_close($DBConnect);
//        echo "<form action='AvailableOpportunities.php' method='post'>\n";
//        // hidden form field hides the field from the user (web browser display)
//        echo "<input type ='hidden' name='internID' value='$internID'>\n";
//        echo "<input type='submit' name='submit' value='View Available Opportunities'>\n";
//        echo "</form>\n";
        echo "<p><a href='AvailableOpportunities.php?" . "internID=$internID'>Available Opportunities" . "</a></p>\n";
    }
    // indicates to go back to fix errors
    if ($errors > 0) {
        echo "Please use your browser's BACK button to return to the form and fix the errors indicated.";
    }
    ?>
</body>
</html>
