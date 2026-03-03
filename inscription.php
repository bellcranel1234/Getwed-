<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // On crypte le mot de passe !

    try {
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user, $email, $pass]);
        
       // echo "<p style='color:green;'>Compte créé avec succès ! Bienvenue sur GetWed.</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erreur : " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rejoindre GetWed</title>
    <style>
        body { font-family: 'Segoe UI',sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f4f2f5; }
        form { background: white; padding: 20px; border-radius: 8px; shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #6c5ce7; color: white; border: none; border-radius:8px; box-sizing: border-box; cursor: pointer; }
        .login-box { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 350px; text-align: center; }
        h1 { color: #6c5ce7; margin-bottom: 20px; }
         a { color: #6c5ce7; text-decoration: none; font-size: 0.85em; }
    </style>
</head>
<body>
    <div class="login-box">
    <form method="POST">
        <h1>Créer un compte GetWed</h1>
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <a href="index.php"><button type="submit">S'inscrire</button></a>
         
    </form>
    <p><a href="login.php">deja un compte ? Login</a></p>
    </div>
</body>
</html>
