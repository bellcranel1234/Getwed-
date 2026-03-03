<?php
require_once 'config.php';
session_start();

$me = $_SESSION['user_id'];
$target = $_GET['id'];

if ($me != $target) {
    // On regarde si on suit déjà
    $check = $pdo->prepare("SELECT * FROM follows WHERE follower_id = ? AND following_id = ?");
    $check->execute([$me, $target]);
    
    if ($check->rowCount() == 0) {
        $ins = $pdo->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
        $ins->execute([$me, $target]);
    } else {
        $del = $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
        $del->execute([$me, $target]);
    }
}
header("Location: profil.php?id=" . $target);
