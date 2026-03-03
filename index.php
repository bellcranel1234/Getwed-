<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

// 1. Récupérer les Lives (On définit bien $lives ici)
$lives_query = $pdo->query("SELECT lives.*, users.username FROM lives JOIN users ON lives.creator_id = users.id WHERE lives.status = 'live'");
$lives = $lives_query ? $lives_query->fetchAll() : [];

// 2. Récupérer les Posts avec toutes les infos nécessaires (cette requête écrase l'ancienne pour éviter les erreurs)
$posts = $pdo->query("
    SELECT posts.*, users.username, users.profile_pic, circles.name as circle_name 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    LEFT JOIN circles ON posts.circle_id = circles.id 
    ORDER BY posts.created_at DESC
")->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GetWed - Flux</title>
    <style>
        :root { --primary: #6c5ce7; --bg: #f8f9fa; --text: #2d3436; --live: #ff4757; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding-bottom: 80px; }
        .header { background: white; padding: 15px; text-align: center; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 100; font-weight: bold; font-size: 1.2em; color: var(--primary); }
        .container { max-width: 500px; margin: auto; padding: 15px; }
        
        .live-section { display: flex; overflow-x: auto; gap: 10px; padding: 10px 0; margin-bottom: 20px; }
        .live-card { background: var(--live); color: white; min-width: 100px; height: 140px; border-radius: 15px; display: flex; flex-direction: column; justify-content: flex-end; padding: 10px; text-decoration: none; font-size: 0.8em; box-shadow: 0 4px 10px rgba(255, 71, 87, 0.3); }

        .post-card { background: white; border-radius: 20px; padding: 15px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .post-header { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9em; }
        .circle-tag { background: #6c5ce7; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.8em; font-weight: bold; }
        video { width: 100%; border-radius: 12px; margin: 10px 0; background: #000; }
        
        .actions { display: flex; gap: 20px; padding: 10px 0; border-top: 1px solid #f9f9f9; }
        .action-link { text-decoration: none; color: #636e72; display: flex; align-items: center; gap: 5px; font-weight: 600; }

        .navbar { position: fixed; bottom: 0; width: 100%; background: white; display: flex; justify-content: space-around; align-items: center; padding: 12px 0; border-top: 1px solid #eee; }
    </style>
</head>
<body>

<div class="header">GetWed</div>

<div class="container">
    <div class="live-section">
        <?php foreach ($lives as $live): ?>
            <a href="salle_live.php?id=<?= $live['id'] ?>" class="live-card">
                <strong>@<?= htmlspecialchars($live['username']) ?></strong>
            </a>
        <?php endforeach; ?>
    </div>

    <?php foreach ($posts as $post): ?>
    <div class="post-card">
        <div class="post-header" style="display: flex; align-items: center; justify-content: space-between;">
            <a href="profil.php?id=<?= $post['user_id'] ?>" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
                <?php 
                $author_img = !empty($post['profile_pic']) ? 'uploads/'.$post['profile_pic'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; 
                ?>
                <img src="<?= $author_img ?>" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 1px solid #eee;">
                <span class="username" style="font-weight: bold; color: var(--primary);">@<?= htmlspecialchars($post['username']) ?></span>
            </a>
            
            <?php if (!empty($post['circle_name'])): ?>
                <span class="circle-tag">🎯 <?= htmlspecialchars($post['circle_name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="content" style="margin-top: 10px;"><?= nl2br(htmlspecialchars($post['text_content'])) ?></div>

        <?php if ($post['content_type'] === 'video'): ?>
            <video controls><source src="<?= htmlspecialchars($post['media_url']) ?>" type="video/mp4"></video>
        <?php endif; ?>

        <div class="actions">
            <a href="like.php?post_id=<?= $post['id'] ?>" class="action-link" style="color:var(--live);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                <?php
                $nb = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
                $nb->execute([$post['id']]);
                echo $nb->fetchColumn();
                ?>
            </a>
            <div class="action-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<nav class="navbar">
    <a href="index.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6c5ce7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></a>
    <a href="recherche.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#636e72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></a>
    <a href="publier.php" style="background:var(--primary); border-radius:50%; padding:10px; margin-top:-30px; box-shadow: 0 4px 10px rgba(108,92,231,0.4);"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></a>
    <a href="profil.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#636e72" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></a>
    <a href="deconnexion.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ff4757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg></a>
</nav>

</body>
</html>