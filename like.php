<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirige vers login si pas connecté
    exit();
}
$user_id = $_SESSION['user_id']; // Récupère l'ID réel


$post_id = $_GET['post_id'];

if (isset($post_id)) {
    // On vérifie si l'utilisateur a déjà liké pour ne pas liker deux fois
    $check = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
    $check->execute([$user_id, $post_id]);
    
    if ($check->rowCount() == 0) {
        $ins = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        $ins->execute([$user_id, $post_id]);
    } else {
        // Si déjà liké, on peut imaginer de "disliker" (enlever le like)
        $del = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
        $del->execute([$user_id, $post_id]);
    }
}

// On repart sur la page d'accueil
header("Location: index.php");
?>
