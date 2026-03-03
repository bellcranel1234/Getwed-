<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirige vers login si pas connecté
    exit();
}
$user_id = $_SESSION['user_id']; // Récupère l'ID réel
 // Test

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    try {
        $sql = "INSERT INTO lives (creator_id, title, status) VALUES (?, ?, 'live')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $title]);
        $live_id = $pdo->lastInsertId();
        header("Location: salle_live.php?id=" . $live_id);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Lancer un Live - GetWed</title>
    <style>
        body { font-family: sans-serif; background: #1a1a1a; color: white; text-align: center; padding: 50px; }
        .setup-box { background: #2d2d2d; padding: 30px; border-radius: 15px; display: inline-block; }
        input { width: 300px; padding: 10px; border-radius: 5px; border: none; margin-bottom: 20px; }
        .btn-live { background: #ff4757; color: white; border: none; padding: 15px 30px; border-radius: 30px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="setup-box">
        <h1>🔴 Studio Live GetWed</h1>
        <form method="POST">
            <input type="text" name="title" placeholder="Titre de votre direct..." required>
            <br>
            <button type="submit" class="btn-live">Lancer la diffusion maintenant</button>
        </form>
    </div>
</body>
</html>
