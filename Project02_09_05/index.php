<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>

<body>
    <form action="UserForm.php?PHPSESSID=<?php echo session_id();?>" method="post">
        <p>
            Enter your name: First:
            <input type="text" name="first">
            Last:
            <input type="text" name="last">
            Enter your company name:
            <input type="text" name="company">
        </p>
        <p>
            Enter your email address:
            <input type="text" name="email">
        </p>
        <p>
            <br>
            <input type="reset" name="reset" value="Reset Registration Form">
            <input type="submit" name="register" value="Register">
        </p>
    </form>
</body>
</html>
