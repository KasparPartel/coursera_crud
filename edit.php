<?php
require_once('pdo.php');
require_once('includes/functions.php');
session_start();
checkUser();

if (isset($_POST['add_button'])) {
    // Validate if email is valid and all forms are filled out etc.
    validateProfile('edit.php?profile_id='.$_GET['profile_id']);
    // Validate position data
    $msg = validatePos();
    if(is_string($msg)) {
        setErrorMsg($msg, 'edit.php?profile_id='.$_GET['profile_id']);
    }

    $stmt = $pdo->prepare('UPDATE profile SET first_name = :fn, last_name = :ln,
                        email = :em, headline = :hl, summary = :sm, image_url = :im
                           WHERE profile_id = :id');
    $stmt->execute(array(':fn' => $_POST['first_name'],
                        ':ln' => $_POST['last_name'],
                        ':em' => $_POST['email'],
                        ':hl' => $_POST['headline'],
                        ':sm' => $_POST['summary'],
                        ':id' => $_GET['profile_id'],
                        ':im' => $_POST['url_image']));
    
    // Delete all data from position table
    $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id=:id');
    $stmt->execute(array(':id' => $_GET['profile_id']));

    // Insert data into position table
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
                             ':id' => $_GET['profile_id']));
        $rank++;
    }                    
    setSuccessMsg('Record edited', 'index.php');
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
    <h1>Editing Profile for UMSI</h1>
    <?php
    printMsg();
    $stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id=:id');
    $stmt->execute(array(':id' => $_GET['profile_id']));
    if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
    ?>
        <form method="post">
            <label for="first_name">First Name: </label><br>
            <input type="text" value=<?=htmlentities($row['first_name'])?>
                name="first_name" id="first_name"><br>
            <label for="last_name">Last Name: </label><br>
            <input type="text" value=<?=htmlentities($row['last_name'])?>
                name="last_name" id="last_name"><br>
            <label for="email">Email: </label><br>
            <input type="text" value=<?=htmlentities($row['email'])?>
                name="email" id="email"><br>
            <label for="headline">Headline: </label><br>
            <input type="text" value=<?=htmlentities($row['headline'])?>
                name="headline" id="headline" size="38"><br>
            <label for="summary">Summary: </label><br>
            <textarea name='summary' id='summary' cols='40' rows='5'><?=htmlentities($row['summary'])?>
            </textarea><br>
            <label for="url_image">Image URL(optional): </label><br>
            <input type="text" 
                   value="<?php if(isset($row['image_url'])) echo htmlentities($row['image_url'])?>"
                   name="url_image" id="url_image"><br>
            <label for="position_btn">Position: </label>
            <input type="submit" value="+" name='position_btn' id='addPos'><br>
            <div id="position_fields">
                <?php 
                $countPos = 1;
                $positions = loadPos($pdo, $_GET['profile_id']);
                foreach ($positions as $pos) { 
                    echo '<div id="position'.$countPos.'"> 
                        <p>Year: <input type="text" name="year'.$countPos.'" value="'.htmlentities($pos['year']).'" /> 
                        <input type="button" value="-" 
                            onclick="$(\'#position'.$countPos.'\').remove();return false;"></p> 
                        <textarea name="desc'.$countPos.'" rows="8" cols="80">'.htmlentities($pos['description']).'</textarea> 
                        </div>';
                    $countPos++;
                }
                ?>
            </div>
            <input type="submit" value="Save" name='add_button'>
            <input type="submit" value="Cancel" name='cancel_button'>
        </form>
    <?php } else {
        setErrorMsg('Bad data', 'index.php');
    } ?>
    <script>
        countPos = <?= $countPos ?>;

        $(document).ready(function() {
            console.log('Document ready called');
            $('#addPos').click(function(event) {
                event.preventDefault();
                if (countPos > 9) {
                    alert('Maximum of nine position entries exceeded');
                    return;
                }
                countPos++;
                console.log('Adding position ' + countPos);
                $('#position_fields').append(
                    '<div id="position' + countPos + '"> \
                    <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
                    <input type="button" value="-" \
                        onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
                    <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea> \
                    </div>');
            });
        });
    </script>
</body>
</html>