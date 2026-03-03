<?php
require_once 'config.php';
session_start();

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirige vers login si pas connecté
    exit();
}
$user_id = $_SESSION['user_id']; // Récupère l'ID réel



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];

    try {
        $sql = "INSERT INTO circles (name, description, creator_id) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $desc, $user_id]);
        echo "<p style='color:green;'>Le Cercle '$name' a été créé avec succès !</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erreur : " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Cercle - GetWed</title>
    <style>
        body { font-family: sans-serif; padding: 50px; background: #f4f4f9; text-align: center; }
        form { background: white; padding: 20px; display: inline-block; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        input, textarea { display: block; width: 300px; margin: 10px auto; padding: 10px; }
        button { background: #6c5ce7; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Fonder un nouveau Cercle</h2>
        <input type="text" name="name" placeholder="Nom du cercle (ex: Développeurs 2026)" required>
        <textarea name="description" placeholder="De quoi va-t-on parler ici ?"></textarea>
        <button type="submit">Créer le Cercle</button>
    </form>
</body>
</html>
