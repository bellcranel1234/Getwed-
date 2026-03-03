<?php
require_once 'config.php';
session_start();

$live_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// On vérifie que c'est bien le créateur qui demande la fin du live
$stmt = $pdo->prepare("UPDATE lives SET status = 'ended', ended_at = NOW() WHERE id = ? AND creator_id = ?");
$stmt->execute([$live_id, $user_id]);

header("Location: index.php");
exit();