<?php
require_once('pdo.php');
require_once('includes/functions.php');
session_start();
checkUser();

if (isset($_POST['add_button'])) {
    // Validate if email is valid and all forms are filled out etc.
    validateProfile('edit.php?profile_id='.$_GET['profile_id']);
    // Validate position data
    $msg = validateRows();
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
    $stmt = $pdo->prepare('DELETE position, education FROM position INNER JOIN education
                           WHERE position.profile_id=education.profile_id and position.profile_id=:id');
    $stmt->execute(array(':id' => $_GET['profile_id']));

    // Insert data into position table
    $rankPos = 1;
    $rankEdu = 1;

    for ($i=1; $i <= 9; $i++) { 
        if (! isset($_POST['year'.$i])) continue;
        if (! isset($_POST['desc'.$i])) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO position (year, description, rank, profile_id)
                                VALUES (:yr, :dc, :rk, :id)');
        $stmt->execute(array(':yr' => $year,
                             ':dc' => $desc,
                             ':rk' => $rankPos,
                             ':id' => $_GET['profile_id']));
        $rankPos++;
    }
    for ($i=1; $i <= 9; $i++) { 
        if (! isset($_POST['yearEdu'.$i])) continue;
        if (! isset($_POST['school'.$i])) continue;
        $year = $_POST['yearEdu'.$i];
        $school = $_POST['school'.$i];
        $institution = loadInstitutions($pdo, $school);
        $school_id = 0;

        if (empty($institution)) {
            $stmt = $pdo->prepare('INSERT INTO Institution (name)
                                   VALUES (:name)');
            $stmt->execute(array(':name' => $school));
        } 
        // TODO make it a prepare statement
        $stmt = $pdo->query('SELECT institution_id FROM institution WHERE name = "'.$school.'"');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $school_id = $row['institution_id'];
        }
        print_r($school_id);

        $stmt = $pdo->prepare('INSERT INTO education (profile_id, institution_id, rank, year)
                                VALUES (:pi, :ii, :rk, :yr)');
        $stmt->execute(array(':yr' => $year,
                             ':ii' => $school_id,
                             ':rk' => $rankEdu,
                             ':pi' => $profile_id));
        $rankEdu++;
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
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
    <div id="container">
        <h1>Editing Profile for UMSI</h1>
        <?php
        printMsg();
        $stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id=:id');
        $stmt->execute(array(':id' => $_GET['profile_id']));
        if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            ?>
            <form method="post">
                <p>
                    <label for="first_name">First Name: </label>
                    <input type="text" value=<?=htmlentities($row['first_name'])?>
                    name="first_name" id="first_name">
                </p>
                <p>
                    <label for="last_name">Last Name: </label>
                    <input type="text" value=<?=htmlentities($row['last_name'])?>
                    name="last_name" id="last_name">
                </p>
                <p>
                    <label for="email">Email: </label>
                    <input type="text" value=<?=htmlentities($row['email'])?>
                    name="email" id="email">
                </p>
                <p>
                    <label for="headline">Headline: </label>
                    <input type="text" value=<?=htmlentities($row['headline'])?>
                    name="headline" id="headline" size="38">
                </p>
                <p>
                    <label for="summary">Summary: </label><br>
                    <textarea name='summary' id='summary' cols='40' rows='5'><?=htmlentities($row['summary'])?>
                    </textarea>
                </p>
                <p>
                    <label for="url_image">Image URL(optional): </label><br>
                    <input type="text" 
                    value="<?php if(isset($row['image_url'])) echo htmlentities($row['image_url'])?>"
                    name="url_image" id="url_image"><br>
                </p>
                <p>
                    <label for="education_btn">Education: </label>
                    <input type="submit" value="+" name='education_btn' id='addEdu'>
                </p>
                <p>
                    <label for="position_btn">Position: </label>
                    <input type="submit" value="+" name='position_btn' id='addPos'><br>
                </p>
                <div id="position_fields">
                <?php 
                    $countPos = 1;
                    $countEdu = 1;
                    $positions = loadRows($pdo, $_GET['profile_id'], 'position');
                    $educations = loadRows($pdo, $_GET['profile_id'], 'education');

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
                <div id="education_fields">
                <?php
                    foreach ($educations as $edu) {
                        $stmt = $pdo->query('SELECT name FROM institution
                                             WHERE institution_id = '.$edu['institution_id']);
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $school = $row['name'];
                        }
                        echo '<div id="education'.$countEdu.'">
                        <p>Year: <input type="text" name="yearEdu'.$countEdu.'" value="'.htmlentities($edu['year']).'" /> 
                        <input type="button" value="-"
                            onclick="$(\'#education'.$countEdu.'\').remove();return false;"></p> 
                        <p>School: <input type="text" name="school'.$countEdu.'" value="'.htmlentities($school).'" class="school" /><hr></p> 
                        </div>';
                        $countEdu++;
                    }
                ?>
                </div>
                <input type="submit" value="Save" name='add_button'>
                <input type="submit" value="Cancel" name='cancel_button'>
            </form>
            <?php } else {
                setErrorMsg('Bad data', 'index.php');
            } ?>
    </div>
    <script>
        countPos = <?= $countPos ?>;
        countEdu = <?= $countEdu ?>

        $(function() {
            console.log('Document ready called');
            $('#addPos, #addEdu').click(function(event) {
                event.preventDefault();
                let id = this.id;
                console.log(id);
                if (id == 'addPos') {
                    if (countPos > 9) {
                        alert('Maximum of nine position entries exceeded');
                        return;
                    };
                    countPos++;
                    console.log('Adding position ' + countPos);
                    $('#position_fields').append(
                        '<div id="position' + countPos + '"> \
                        <p>Year: <input type="text" name="yearPos' + countPos + '" value="" /> \
                        <input type="button" value="-" \
                            onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
                        <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea> \
                        </div>');
                };
                
                if (id == 'addEdu') {
                    if (countEdu > 9) {
                        alert('Maximum of nine education entries exceeded');
                        return;
                    };
                    countEdu++;
                    console.log('Adding education ' + countEdu);
                    $('#education_fields').append(
                        '<div id="education' + countEdu + '"> \
                        <p>Year: <input type="text" name="yearEdu' + countEdu + '" value="" /> \
                        <input type="button" value="-" \
                            onclick="$(\'#education' + countEdu + '\').remove();return false;"></p> \
                        <p>School: <input type="text" name="school' + countEdu + '" value="" class="school" /><hr></p> \
                        </div>');
                    $(".school").autocomplete({source: 'school.php'});
                }
            });
        });
    </script>
</body>
</html>