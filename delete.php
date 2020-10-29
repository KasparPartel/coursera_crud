<?php
require_once('pdo.php');
require_once('includes/functions.php');
session_start();
checkUser();

if (isset($_POST['delete'])) {
    $stmt = $pdo->prepare('SELECT user_id from profile where profile_id = :id');
    $stmt->execute(array(':id' => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row['user_id'] === $_SESSION['user_id']) {
        $stmt = $pdo->prepare('DELETE FROM profile WHERE profile_id=:id');
        $stmt->execute(array(':id' => $_GET['profile_id']));
        setSuccessMsg('Record Deleted', 'index.php');
    } else {
        setErrorMsg('You don\'t have rights to delete this entry', 'index.php');
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
    <h1>Deleting Profile</h1>
    <?php
    $stmt = $pdo->prepare('SELECT first_name, last_name FROM profile WHERE profile_id=:id');
    $stmt->execute(array(':id' => $_GET['profile_id']));
    if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) { ?>
        <p>First Name: <?=htmlentities($row['first_name'])?></p>
        <p>Last Name: <?=htmlentities($row['last_name'])?></p>
        <form method="post">
            <input type="submit" value="Delete" name='delete'>
            <input type="submit" value="Cancel" name='cancel_button'>
        </form>
    <?php } else {
        setErrorMsg('Bad data', 'index.php');
    }
    ?>
</body>
</html>