<?php
require_once('pdo.php');
require_once('includes/functions.php');
session_start();

if (isset($_POST['login_button'])) {
    if (!empty($_POST['email']) && !empty($_POST['pass'])) {
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $stmt = $pdo->prepare('SELECT user_id, name FROM users 
                                WHERE email=:em AND password=:pw');
            $stmt->execute(array(':em' => $_POST['email'], 
                            ':pw' => md5('XyZzy12*_'.$_POST['pass'])));
            if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['user_id'] = $row['user_id'];
                    header('Location: index.php');
                    return;
            } else {
                setErrorMsg('Incorrect password', 'login.php');                
            }    
        } else {
            setErrorMsg('Email is not correct', 'login.php');
        }
    } else {
        setErrorMsg('Both fields must be filled out', 'login.php');
    }
}

checkCancel('cancel_button');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once('includes/head.php') ?>
</head>
<body>
    <h1>Please Log In</h1>
    <?php printMsg() ?>
    <p style="color:red" id="error"></p>
    
    <script>
        function doValidate() {
            console.log('Validating...');
            var uname = document.getElementById('email_form').value;
            var pwd = document.getElementById('password_form').value;
            var mailFormat = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            try  {
                console.log('Validating if both forms are filled out');
                if (!uname || !pwd) {
                    console.log('Both fields are not filled out');
                    document.getElementById("error").innerHTML = "All fields required";
                    // alert("Both fields must be filled out");
                    return false;
                }
                console.log('Both fields are filled out');
                console.log('Validating if email is correct');
                if (!(uname.match(mailFormat))) {
                    console.log('Email is not correct');
                    document.getElementById("error").innerHTML = "Invalid email address";
                    // alert("Invalid email address");
                    return false;
                } else {
                    console.log('Email is correct');
                    return true;
                }
            } catch(e) {
                return false;
            }
            
        }
    </script>
    <form method="post">
        <label for="email_form">Email</label>
        <input type="text" name='email' id='email_form'><br>
        <label for="password_form">Password</label>
        <input type="password" name='pass' id='password_form'><br>
        <input type="submit" value='Log In' name='login_button' onclick="return doValidate()">
        <input type="submit" value='Cancel' name='cancel_button'>
    </form>
</body>
</html>