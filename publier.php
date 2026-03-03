<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$circles = $pdo->query("SELECT id, name FROM circles")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = $_POST['text_content'];
    $circle = ($_POST['circle_id'] == "0") ? null : $_POST['circle_id'];
    $media_url = "";
    $type = "text";

    // Est-ce qu'une vidéo a été envoyée ?
    if (!empty($_FILES['video_file']['name'])) {
        $nom_video = time() . '_' . $_FILES['video_file']['name']; // On donne un nom unique
        $destination = "uploads/" . $nom_video;

        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $destination)) {
            $media_url = $destination; // On enregistre le chemin du dossier
            $type = "video";
        }
    }

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, content_type, text_content, media_url, circle_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $type, $text, $media_url, $circle]);
    
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GetWed - Publier</title>
    <style>
        body { font-family: sans-serif; background: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .form-box { background: white; padding: 30px; border-radius: 20px; width: 90%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        select, textarea, input, button { width: 100%; margin-bottom: 15px; padding: 12px; border-radius: 10px; border: 1px solid #ddd; box-sizing: border-box; }
        button { background: #6c5ce7; color: white; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
<div class="form-box">
    <h2 style="color:#6c5ce7; text-align:center;">Nouvelle Vibe</h2>
    <form method="POST" enctype="multipart/form-data">
    <select name="circle_id">
        <option value="0">🌍 Flux Général</option>
        <?php foreach($circles as $c): ?>
            <option value="<?= $c['id'] ?>">🎯 <?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <textarea name="text_content" placeholder="Quoi de neuf ?" required></textarea>

    <label style="display:block; margin-bottom:10px; font-weight:bold;">Ajouter une vidéo :</label>
    <input type="file" name="video_file" accept="video/*">

    <button type="submit">Publier sur GetWed</button>
</form>

</div>
</body>
</html>
