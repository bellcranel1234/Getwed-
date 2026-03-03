<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search_results_users = [];
$search_results_circles = [];
$query = "";

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $query = $_GET['q'];
    $search = "%$query%";

    // 1. Chercher des utilisateurs
    $stmt_u = $pdo->prepare("SELECT id, username FROM users WHERE username LIKE ? LIMIT 5");
    $stmt_u->execute([$search]);
    $search_results_users = $stmt_u->fetchAll();

    // 2. Chercher des Cercles
    $stmt_c = $pdo->prepare("SELECT id, name, description FROM circles WHERE name LIKE ? OR description LIKE ? LIMIT 5");
    $stmt_c->execute([$search, $search]);
    $search_results_circles = $stmt_c->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche - GetWed</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .search-container { max-width: 500px; margin: auto; }
        .search-input { width: 100%; padding: 15px; border-radius: 30px; border: 1px solid #ddd; font-size: 1em; outline: none; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .result-section { background: white; border-radius: 15px; margin-top: 20px; padding: 15px; }
        .result-item { display: flex; align-items: center; padding: 10px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; }
        .result-item:hover { background: #f8f9fa; }
        .badge { font-size: 0.7em; background: #6c5ce7; color: white; padding: 3px 8px; border-radius: 10px; margin-left: 10px; }
        h3 { color: #555; font-size: 0.9em; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body>

<div class="search-container">
    <form action="recherche.php" method="GET">
        <input type="text" name="q" class="search-input" placeholder="Chercher un ami ou un cercle..." value="<?php echo htmlspecialchars($query); ?>" autofocus>
    </form>

    <?php if ($query): ?>
        <div class="result-section">
            <h3>Utilisateurs</h3>
            <?php if ($search_results_users): ?>
                <?php foreach ($search_results_users as $u): ?>
                    <a href="profil.php?id=<?php echo $u['id']; ?>" class="result-item">
                        @<?php echo htmlspecialchars($u['username']); ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="font-size: 0.9em; color: #888;">Aucun utilisateur trouvé.</p>
            <?php endif; ?>
        </div>

        <div class="result-section">
            <h3>Cercles</h3>
            <?php if ($search_results_circles): ?>
                <?php foreach ($search_results_circles as $c): ?>
                    <a href="cercle.php?id=<?php echo $c['id']; ?>" class="result-item">
                        🎯 <?php echo htmlspecialchars($c['name']); ?>
                        <span class="badge">Rejoindre</span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="font-size: 0.9em; color: #888;">Aucun cercle trouvé.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
