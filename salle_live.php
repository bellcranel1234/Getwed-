<?php
require_once 'config.php';
session_start();

$live_id = $_GET['id'] ?? 0;
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirige vers login si pas connecté
    exit();
}
$user_id = $_SESSION['user_id']; // Récupère l'ID réel



// Récupérer les infos du live
$stmt = $pdo->prepare("SELECT lives.*, users.username FROM lives JOIN users ON lives.creator_id = users.id WHERE lives.id = ?");
$stmt->execute([$live_id]);
$live = $stmt->fetch();

if (!$live) { die("Live introuvable."); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>🔴 <?php echo htmlspecialchars($live['title']); ?> - GetWed</title>
    <style>
        body { font-family: sans-serif; background: #000; color: white; margin: 0; display: flex; height: 100vh; }
        .video-side { flex: 3; display: flex; flex-direction: column; justify-content: center; align-items: center; background: #111; border-right: 1px solid #333; }
        .chat-side { flex: 1; display: flex; flex-direction: column; background: #1a1a1a; }
        video { width: 90%; border-radius: 15px; box-shadow: 0 0 20px rgba(255, 71, 87, 0.5); }
        .chat-messages { flex: 1; padding: 15px; overflow-y: auto; font-size: 0.9em; }
        .chat-input { padding: 15px; border-top: 1px solid #333; }
        input { width: 100%; padding: 10px; border-radius: 20px; border: none; }
        .live-badge { background: #ff4757; padding: 5px 10px; border-radius: 5px; position: absolute; top: 20px; left: 20px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="live-badge">EN DIRECT</div>

    <div class="video-side">
        <h1><?php echo htmlspecialchars($live['title']); ?></h1>
        <video id="webcam" autoplay playsinline></video>
        <p>Diffusé par @<?php echo htmlspecialchars($live['username']); ?></p>
    </div>

    <div class="chat-side">
        <div class="chat-messages" id="chat">
            <p><i>Bienvenue dans le chat de GetWed ! Respectez la vibe.</i></p>
        </div>
        <div class="chat-input">
            <input type="text" id="msg" placeholder="Envoyer un message...">
        </div>
    </div>
    <?php if ($_SESSION['user_id'] == $live_data['creator_id']): ?>
    <div style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <a href="terminer_live.php?id=<?= $live_id ?>" 
           style="background: #ff4757; color: white; padding: 10px 20px; border-radius: 30px; text-decoration: none; font-weight: bold; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
           Terminer le Live ✕
        </a>
    </div>
<?php endif; ?>

    <script>
        // SCRIPT POUR CAPTURER LA WEBCAM (Le côté interactif de GetWed)
        const video = document.getElementById('webcam');

        async function startWebcam() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                video.srcObject = stream;
            } catch (err) {
                console.error("Erreur caméra : ", err);
                alert("Impossible d'accéder à la caméra. Vérifie les autorisations.");
            }
        }

        // On lance la caméra au chargement
        startWebcam();

        // Simulation simple de chat (juste visuel pour le moment)
        document.getElementById('msg').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                const chat = document.getElementById('chat');
                chat.innerHTML += `<p><b>Moi:</b> ${this.value}</p>`;
                this.value = '';
                chat.scrollTop = chat.scrollHeight;
            }
        });
    </script>
</body>
</html>
