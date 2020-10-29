<?php
require_once('pdo.php');
require_once('includes/functions.php');
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once('includes/head.php') ?>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <?php
    echo("<h1>Kaspar J. Pärtel's Profile Registry </h1>");
    printMsg();
    if (isset($_SESSION['user_id'])) {
    ?>
        <table>
            <tr><th>Image</th><th>Name</th><th>Headline</th><th>Action</th></tr>
            <?php
            $stmt = $pdo->query('SELECT first_name, last_name, headline, image_url, profile_id from profile');
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo('<tr>');
                echo('<td><img src="'.$row['image_url'].'" alt="'.$row['image_url'].'"</td>');                   
                echo('<td><a href="view.php?profile_id='.$row['profile_id'].'">'
                    .htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a></td>');
                echo('<td>'.htmlentities($row['headline']).'</td>');
                echo('<td><a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>');
                echo(' <a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a></td>');
                echo('</tr>');
            }
            ?>
        </table>
        <p><a href="add.php">Add New Entry</a></p>
        <p><a href="logout.php">Logout</a></p>
        
    <?php } else { ?>
        <p><a href="login.php">Please log in</a></p>
        <table style='border: 1px solid black'>
        <?php
        $stmt = $pdo->query('SELECT first_name, last_name, headline, image_url, profile_id from profile');
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo('<tr>');
            echo('<td><a href="view.php?profile_id='.$row['profile_id'].'">'
                .htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a></td>');
            echo('<td>'.htmlentities($row['headline']).'</td>');
            echo('</tr>');
        } }?>
        </table>
    
</body>
</html>