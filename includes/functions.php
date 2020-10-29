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

function validatePos() {
    for ($i=1; $i <= 9; $i++) { 
        echo '<p>'.$i.'</p>';
        if (! isset($_POST['year'.$i]) ) continue;
        if (! isset($_POST['desc'.$i]) ) continue;
        
        if (strlen($_POST['year'.$i]) == 0 || strlen($_POST['desc'.$i]) == 0) {
            return 'All fields required';
        }

        if (! is_numeric($_POST['year'.$i])) {
            return 'Year must be numeric';
        }
    }
    return true;
}

function loadPos($pdo, $profile_id) {
    $stmt = $pdo->prepare('SELECT * FROM position
                           WHERE profile_id = :id
                           ORDER BY rank');
    $stmt->execute(array(':id' => $profile_id));
    $positions = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($positions, $row);
    }
    return $positions;
}

