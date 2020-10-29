<?php
require_once('pdo.php');
require_once('includes/functions.php');
session_start();
checkUser();

// Check if add button was pressed and verify data
if (isset($_POST['add_button'])) {
    // Validate if email is valid etc.
    validateProfile('add.php');
    // Validate position data
    $msg = validatePos();
    if (is_string($msg)) {
        setErrorMsg($msg, 'add.php');
    }
    // Insert data into profile table and set success message
    $stmt = $pdo->prepare('INSERT INTO profile (user_id, first_name, last_name, email,
                        headline, summary, image_url) VALUES (:id, :fn, :ln, :em, :hl, :sm, :im)');
    $stmt->execute(array(':id' => $_SESSION['user_id'],
                        ':fn' => $_POST['first_name'],
                        ':ln' => $_POST['last_name'],
                        ':em' => $_POST['email'],
                        ':hl' => $_POST['headline'],
                        ':sm' => $_POST['summary'],
                        ':im' => $_POST['url_image']));
    $profile_id = $pdo->lastInsertId();

    // Insert data into positions table if there is any
    $rank = 1;
    for ($i=1; $i <= 9; $i++) { 
        if (! isset($_POST['year'.$i])) continue;
        if (! isset($_POST['desc'.$i])) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO position (year, description, rank, profile_id)
                                VALUES (:yr, :dc, :rk, :id)');
        $stmt->execute(array(':yr' => $year,
                             ':dc' => $desc,
                             ':rk' => $rank,
                             ':id' => $profile_id));
        $rank++;
    }
    setSuccessMsg('Record added', 'index.php');
}

// Check if cancel button is pressed and direct back to index.php
checkCancel('cancel_button');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once('includes/head.php') ?>
    <link rel="stylesheet" href="css/add.css" >
</head>
<body>
<div id="container">
    <h1>Adding Profile for UMSI</h1>
    <?= printMsg() ?>
    <form action="" method="post">
        <p>
            <label for="first_name">First Name: </label>
            <input type="text" name="first_name" id="first_name">
        </p>
        <p>
            <label for="last_name">Last Name: </label>
            <input type="text" name="last_name" id="last_name">
        </p>
        <p>
            <label for="email">Email: </label>
            <input type="text" name="email" id="email">
        </p>
        <p>
            <label for="headline">Headline: </label>
            <input type="text" name="headline" id="headline">
        </p>
        <p>
            <label for="summary">Summary: </label><br>
            <textarea name='summary' id='summary' cols='80' rows='8'></textarea>
        </p>
        <p>
            <label for="url_image">Image URL(optional): </label>
            <input type="text" name="url_image" id="url_image">
        </p>
        <p>
            <label for="education_btn">Education: </label>
            <input type="submit" value="+" name='education_btn' id='addEdu'>
        </p>
        <p>
            <label for="position_btn">Position: </label>
            <input type="submit" value="+" name='position_btn' id='addPos'>
        </p>
        <div id="education_fields"></div>
        <div id="position_fields"></div>
        <input type="submit" value="Add" name='add_button'>
        <input type="submit" value="Cancel" name='cancel_button'>
    </form>
</div>
    <script>
        countPos = 0;
        countEdu = 0;

        $(function() {
            console.log('Document ready called');
            $('#addPos, #addEdu').click(function(event) {
                event.preventDefault();
                let id = this.id;
                console.log(id);
                if (id == 'addPos') {
                    if (countPos >= 9) {
                        alert('Maximum of nine position entries exceeded');
                        return;
                    };
                    countPos++;
                    console.log('Adding position ' + countPos);
                    $('#position_fields').append(
                        '<div id="position' + countPos + '"> \
                        <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
                        <input type="button" value="-" \
                            onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
                        <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea> \
                        </div>');
                };
                
                if (id == 'addEdu') {
                    if (countEdu >= 9) {
                        alert('Maximum of nine education entries exceeded');
                        return;
                    };
                    countEdu++;
                    console.log('Adding education ' + countEdu);
                    $('#education_fields').append(
                        '<div id="education' + countEdu + '"> \
                        <p>Year: <input type="text" name="year' + countEdu + '" value="" /> \
                        <input type="button" value="-" \
                            onclick="$(\'#education' + countEdu + '\').remove();return false;"></p> \
                        <p>School: <input type="text" name="school' + countEdu + '" value="" class="school" /><hr></p> \
                        </div>');
                    $(".school").autocomplete({source: 'school.php'});
                }
            });
        });





        // $(document).ready(function() {
        //     $('#addEdu').click(function(event) {
        //         event.preventDefault();
        //         if (countEdu >= 9) {
        //             alert('Maximum of nine education entries exceeded');
        //             return;
        //         }
        //         countEdu++;
        //         console.log('Adding education ' + countEdu);
        //         $('#education_fields').append(
        //             '<div id="education' + countEdu + '"> \
        //             <p>Year: <input type="text" name="year' + countEdu + '" value="" /> \
        //             <input type="button" value="-" \
        //                 onclick="$(\'#education' + countEdu + '\').remove();return false;"></p> \
        //             <p>School: <input type="text" class="school" name="school' + countEdu + '" value="" /></p> \
        //             </div>');
        //         $('.school').autocomplete({'school.php'});
        //     });
        // });
    </script>
</body>
</html>