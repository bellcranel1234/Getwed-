<?php
require_once 'config.php';
session_start();

$profile_id = $_GET['id'] ?? $_SESSION['user_id'];
$my_id = $_SESSION['user_id'];

// Récupération des infos utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$u = $stmt->fetch();

// Stats (Abonnés, Abonnements, Posts)
$nb_posts = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
$nb_posts->execute([$profile_id]);
$total_posts = $nb_posts->fetchColumn();

$nb_followers = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE following_id = ?");
$nb_followers->execute([$profile_id]);
$followers_count = $nb_followers->fetchColumn();

$nb_following = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ?");
$nb_following->execute([$profile_id]);
$following_count = $nb_following->fetchColumn();

// Vérifier si je suis déjà abonné
$check_follow = $pdo->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
$check_follow->execute([$my_id, $profile_id]);
$is_following = $check_follow->rowCount() > 0;

// Récupérer les publications pour la grille
$posts = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$posts->execute([$profile_id]);
$user_posts = $posts->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GetWed - @<?= htmlspecialchars($u['username']) ?></title>
    <style>
        :root { --primary: #6c5ce7; --bg: #ffffff; --text: #2d3436; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica; background: var(--bg); margin: 0; padding-bottom: 70px; }
        
        .header-profile { padding: 30px 20px; border-bottom: 1px solid #efefef; max-width: 600px; margin: auto; }
        .top-info { display: flex; align-items: center; gap: 25px; margin-bottom: 20px; }
        .profile-img { width: 85px; height: 85px; border-radius: 50%; object-fit: cover; border: 2px solid #efefef; }
        
        .username-row { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
        .username-row h2 { margin: 0; font-weight: 300; font-size: 24px; }
        
        .btn { padding: 8px 16px; border-radius: 8px; border: 1px solid #dbdbdb; background: white; font-weight: 600; cursor: pointer; text-decoration: none; color: black; font-size: 14px; }
        .btn-follow { background: var(--primary); color: white; border: none; }
        
        .stats-row { display: flex; gap: 20px; margin-bottom: 15px; font-size: 15px; }
        .bio-section { line-height: 1.4; font-size: 14px; }
        .website { color: #00376b; text-decoration: none; font-weight: 600; }

        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 3px; max-width: 900px; margin: 20px auto; }
        .grid-item { aspect-ratio: 1/1; background: #fafafa; position: relative; cursor: pointer; }
        .grid-item video, .grid-item img { width: 100%; height: 100%; object-fit: cover; }
        .grid-item .icon { position: absolute; top: 8px; right: 8px; }

        /* Nav Basse */
        .navbar { position: fixed; bottom: 0; width: 100%; background: white; display: flex; justify-content: space-around; padding: 12px 0; border-top: 1px solid #dbdbdb; }
    </style>
</head>
<body>

<div class="header-profile">
    <div class="top-info">
        <img src="uploads/<?= $u['profile_pic'] ?>" class="profile-img" onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png'">
        <div class="user-actions">
            <div class="username-row">
                <h2><?= htmlspecialchars($u['username']) ?></h2>
                <?php if($profile_id == $my_id): ?>
                    <a href="edit_profil.php" class="btn">Modifier le profil</a>
                <?php else: ?>
                    <a href="follow.php?id=<?= $profile_id ?>" class="btn <?= !$is_following ? 'btn-follow' : '' ?>">
                        <?= $is_following ? 'Désabonner' : 'S\'abonner' ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="stats-row">
                <span><strong><?= $total_posts ?></strong> publications</span>
                <span><strong><?= $followers_count ?></strong> abonnés</span>
                <span><strong><?= $following_count ?></strong> abonnements</span>
            </div>
            <div class="bio-section">
                <strong><?= htmlspecialchars($u['username']) ?></strong><br>
                <span><?= nl2br(htmlspecialchars($u['bio'])) ?></span><br>
                <?php if($u['website']): ?>
                    <a href="<?= htmlspecialchars($u['website']) ?>" class="website" target="_blank"><?= str_replace(['http://', 'https://'], '', $u['website']) ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="grid">
    <?php foreach($user_posts as $p): ?>
        <div class="grid-item" onclick="window.location.href='view_post.php?id=<?= $p['id'] ?>'">
            
            <?php if($p['content_type'] == 'video'): ?>
                <a href="view_post.php?id=<?=$p['id']?>"><video src="<?= htmlspecialchars($p['media_url']) ?>" muted></video></a>
                <div class="icon" style="position:absolute; top:8px; right:8px;">🎥</div>
            
            <?php else: ?>
                <div class="text-preview" style="background: #f0f0f0; height:100%; padding:10px; font-size:11px; display:flex; align-items:center; justify-content:center; text-align:center;">
                    <a href="view_post.php?id=<?=$p['id']?>">"<?= mb_strimwidth(htmlspecialchars($p['text_content']), 0, 60, "...") ?>"</a>
                </div>
                <div class="icon" style="position:absolute; top:8px; right:8px;">📝</div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>
</div>

<nav class="navbar">
    <a href="index.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></a>
    <a href="recherche.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></a>
    <a href="publier.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg></a>
    <a href="profil.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="black" stroke="black" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></a>
</nav>

</body>
</html>