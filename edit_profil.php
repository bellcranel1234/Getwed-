<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

// 1. Récupérer les données actuelles
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$u = $stmt->fetch();

$message = "";

// 2. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = $_POST['bio'];
    $website = $_POST['website'];
    $profile_pic = $u['profile_pic']; // Par défaut, on garde l'ancienne

    // Gestion de l'upload de la photo de profil
    if (!empty($_FILES['avatar']['name'])) {
        $img_name = time() . '_' . $_FILES['avatar']['name'];
        $target = "uploads/" . $img_name;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
            $profile_pic = $img_name;
        }
    }

    // Mise à jour de la base de données
    $update = $pdo->prepare("UPDATE users SET bio = ?, website = ?, profile_pic = ? WHERE id = ?");
    if ($update->execute([$bio, $website, $profile_pic, $user_id])) {
        $message = "Profil mis à jour avec succès !";
        // Rafraîchir les données pour l'affichage
        header("Location: profil.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Profil - GetWed</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
        .edit-container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        h2 { color: #6c5ce7; text-align: center; }
        label { display: block; margin: 15px 0 5px; font-weight: bold; font-size: 0.9em; }
        input, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 10px; box-sizing: border-box; font-family: inherit; }
        textarea { height: 100px; resize: none; }
        .btn-save { background: #6c5ce7; color: white; border: none; width: 100%; padding: 15px; border-radius: 10px; font-weight: bold; margin-top: 20px; cursor: pointer; }
        .current-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; display: block; margin: 10px auto; border: 2px solid #6c5ce7; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #888; text-decoration: none; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Modifier mon profil</h2>
    
    <?php if($message) echo "<p style='color:green; text-align:center;'>$message</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <center>
            <img src="uploads/<?= $u['profile_pic'] ?>" class="current-avatar" onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png'">
            <label for="avatar" style="color:#6c5ce7; cursor:pointer;">Changer la photo de profil</label>
            <input type="file" id="avatar" name="avatar" accept="image/*" style="display:none;" onchange="this.form.submit">
        </center>

        <label>Biographie</label>
        <textarea name="bio" placeholder="Racontez votre histoire..."><?= htmlspecialchars($u['bio'] ?? '') ?></textarea>

        <label>Lien Site Web / Portfolio</label>
        <input type="url" name="website" placeholder="https://votre-lien.com" value="<?= htmlspecialchars($u['website'] ?? '') ?>">

        <button type="submit" class="btn-save">Enregistrer les modifications</button>
        <a href="profil.php" class="back-link">Annuler et retourner au profil</a>
    </form>
</div>

</body>
</html>
