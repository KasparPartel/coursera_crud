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
        $positions = loadRows($pdo, $_GET['profile_id'], 'position');
        $educations = loadRows($pdo, $_GET['profile_id'], 'education');

        if (isset($positions)) {
            echo '<p>Positions: </p>';
            echo '<ul>';
            foreach ($positions as $pos) {
                echo '<li>';
                echo htmlentities($pos['year']).' / ';
                echo htmlentities($pos['description']);
                echo '</li>';
            }
            echo '</ul>';
        }
        if (isset($educations)) {
            echo '<p>Educations: </p>';
            echo '<ul>';
            foreach ($educations as $edu) {
                $stmt = $pdo->query("SELECT name FROM Institution
                                       WHERE institution_id = ".$edu['institution_id']);
                $school = $stmt->fetch(PDO::FETCH_ASSOC);
                echo '<li>';
                echo htmlentities($edu['year']).' / ';
                echo htmlentities($school['name']);
                echo '</li>';
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