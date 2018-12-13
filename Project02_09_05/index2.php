<!--THIS DOES NOT WORK!-->
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Page</title>
</head>

<body>
    <form action="EditInfo.php?PHPSESSID=<?php echo session_id();?>" method="post">
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
            <input type="submit" name="Enter" value="Enter">
        </p>
    </form>
</body>
</html>
