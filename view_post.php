<?php
require_once 'config.php';
session_start();

$post_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT posts.*, users.username, users.profile_pic FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) { die("Post introuvable."); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Post de @<?= htmlspecialchars($post['username']) ?></title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; padding: 20px; }
        .post-container { background: white; padding: 20px; border-radius: 15px; max-width: 500px; width: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        video { width: 100%; border-radius: 10px; }
        .back { display: block; margin-bottom: 15px; color: #6c5ce7; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="post-container">
        <a href="javascript:history.back()" class="back">← Retour</a>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
             <img src="uploads/<?= $post['profile_pic'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png'" style="width:40px; height:40px; border-radius:50%;">
             <strong>@<?= htmlspecialchars($post['username']) ?></strong>
        </div>
        <p><?= nl2br(htmlspecialchars($post['text_content'])) ?></p>
        <?php if($post['content_type'] == 'video'): ?>
            <video src="<?= htmlspecialchars($post['media_url']) ?>" controls autoplay></video>
        <?php endif; ?>
    </div>
</body>
</html>