<?php
function checkUser() {
    if (!isset($_SESSION['user_id'])) {
        die('Not logged in');
    }
}

function checkCancel($btn_name) {
    if (isset($_POST[$btn_name])) {
        header('Location: index.php');
        return;
    }
}

function setSuccessMsg($msg, $location) {
    $_SESSION['success'] = $msg;
    header('Location: '.$location);
    exit();
}

function setErrorMsg($msg, $location) {
    $_SESSION['error'] = $msg;
    header('Location: '.$location);
    exit();
}

function printMsg() {
    if (isset($_SESSION['success'])) {
        echo "<p style='color:green'>".$_SESSION['success']."</p>";
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo "<p style='color:red'>".$_SESSION['error']."</p>";
        unset($_SESSION['error']);
    }
}

function validateProfile($loc) {
    if(empty($_POST['first_name']) || empty($_POST['last_name']) ||
       empty($_POST['email']) || empty($_POST['headline']) || empty($_POST['summary'])) {
        setErrorMsg('All fields are required', $loc);
    }
    // Check if email address is valid
    if(! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        setErrorMsg('Email address must contain @', $loc);
    }
    // Check if image url is set and valid
    if (!empty($_POST['url_image'])) {
        $url = filter_var($_POST['url_image'], FILTER_SANITIZE_URL);
        if (!filter_var($_POST['url_image'],FILTER_VALIDATE_URL)) {
            setErrorMsg('Url is not correct', $loc);
        }
    }
}

function validateRows() {
    for ($i=1; $i <= 9; $i++) {   
        if (! isset($_POST['yearPos'.$i]) ) continue;
        if (! isset($_POST['desc'.$i]) ) continue;
        
        if (strlen($_POST['yearEdu'.$i]) == 0 || strlen($_POST['desc'.$i]) == 0) {
            return 'All fields required';
        }

        if (! is_numeric($_POST['yearEdu'.$i])) {
            return 'Year must be numeric';
        }
    }
    for ($i=1; $i <= 9; $i++) {
        if (! isset($_POST['yearEdu'.$i]) ) continue;
        if (! isset($_POST['school'.$i]) ) continue;
        
        if (strlen($_POST['yearEdu'.$i]) == 0 || strlen($_POST['school'.$i]) == 0) {
            return 'All fields required';
        }

        if (! is_numeric($_POST['yearEdu'.$i])) {
            return 'Year must be numeric';
        }
    }
    return true;
}

// function validateEdu() {
//     for ($i=1; $i <= 9; $i++) { 
//         if (! isset($_POST['yearEdu'.$i]) ) continue;
//         if (! isset($_POST['school'.$i]) ) continue;
        
//         if (strlen($_POST['yearEdu'.$i]) == 0 || strlen($_POST['school'.$i]) == 0) {
//             return 'All fields required';
//         }

//         if (! is_numeric($_POST['yearEdu'.$i])) {
//             return 'Year must be numeric';
//         }
//     }
//     return true;
// }

// function loadPos($pdo, $profile_id) {
//     $stmt = $pdo->prepare('SELECT * FROM position
//                            WHERE profile_id = :id
//                            ORDER BY rank');
//     $stmt->execute(array(':id' => $profile_id));
//     $positions = array();
//     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//         array_push($positions, $row);
//     }
//     return $positions;
// }

function loadRows($pdo, $profile_id, $db) {
    $stmt = $pdo->prepare("SELECT * FROM ".$db."
                           WHERE profile_id = :id
                           ORDER BY rank");
    $stmt->execute(array(':id' => $profile_id));
    $rows = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($rows, $row);
    }
    return $rows;
}

function loadInstitutions($pdo, $name) {
    $stmt = $pdo->prepare('SELECT * FROM institution
                           WHERE name = :name');
    $stmt->execute(array(':name' => $name));
    $institutions = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($institutions, $row);
    }
    return $institutions;
}

