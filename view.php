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
</head>
<body>
    <h1>Profile information</h1>
    <?php
    $stmt = $pdo->prepare('SELECT first_name, last_name, email, headline, summary 
                         FROM profile WHERE profile_id=:id');
    $stmt->execute(array(':id' => $_GET['profile_id']));
    if(($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
    ?>
        <p>First Name: <?=htmlentities($row['first_name'])?></p>
        <p>Last Name: <?=htmlentities($row['last_name'])?></p>
        <p>Email: <?=htmlentities($row['email'])?></p>
        <p>Headline: <br><?=htmlentities($row['headline'])?></p>
        <p>Summary: <br><?=htmlentities($row['summary'])?></p>
        <?php
        $positions = loadPos($pdo, $_GET['profile_id']);
        if (isset($positions)) {
            echo '<p>Positions: </p>';
            echo '<ul>';
            foreach ($positions as $pos) {
                echo '<li>'.htmlentities($pos['description']).'</li>';
            }
            echo '</ul>';
        }
        ?>
        <a href="index.php">Done</a>
    <?php } else {
        setErrorMsg("Bad data", 'index.php');
    } ?>
</body>
</html>