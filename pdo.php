<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=crud_users', 'root', '123');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
