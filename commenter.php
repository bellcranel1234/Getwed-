<?php
require_once 'config.php';
session_start();
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirige vers login si pas connecté
    exit();
}
$user_id = $_SESSION['user_id']; // Récupère l'ID réel



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $text = $_POST['comment_text'];

    if (!empty($text)) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $text]);
    }
}

header("Location: index.php");
?>