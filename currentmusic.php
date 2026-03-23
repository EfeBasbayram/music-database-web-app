<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'efe_basbayram';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_SESSION['user_id'];

// Get the last played song for the user
$stmt = $conn->prepare("SELECT S.*, AL.name AS album_name, A.name AS artist_name, C.country_name, PH.playtime FROM PLAY_HISTORY PH JOIN SONGS S ON PH.song_id = S.song_id JOIN ALBUMS AL ON S.album_id = AL.album_id JOIN ARTISTS A ON AL.artist_id = A.artist_id JOIN COUNTRY C ON A.country_id = C.country_id WHERE PH.user_id = ? ORDER BY PH.playtime DESC LIMIT 1");
$stmt->execute([$user_id]);
$song = $stmt->fetch(PDO::FETCH_ASSOC);

$status = 'stopped';
if (isset($_SESSION['current_music_status'])) {
    $status = $_SESSION['current_music_status'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['play'])) {
        $status = 'playing';
        $_SESSION['current_music_status'] = 'playing';
    } elseif (isset($_POST['stop'])) {
        $status = 'stopped';
        $_SESSION['current_music_status'] = 'stopped';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Currently Playing Music</title>
    <style>
        body { font-family: Arial, sans-serif; background: rgba(16,24,42,0.95); color: #fff; margin: 0; }
        .modal {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(16,24,42,0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal-content {
            background: #1a2238;
            border-radius: 20px;
            padding: 2.5rem 3.5rem;
            box-shadow: 0 4px 32px rgba(16,24,42,0.28);
            min-width: 350px;
            max-width: 90vw;
            text-align: center;
        }
        .song-img { width: 120px; height: 120px; border-radius: 16px; object-fit: cover; margin-bottom: 1rem; background: #23395d; box-shadow: 0 2px 8px rgba(16,24,42,0.18); }
        .song-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem; color: #fff; }
        .song-artist { font-size: 1.1rem; margin-bottom: 0.5rem; }
        .song-album { color: #e0e6f7; margin-bottom: 1rem; }
        .controls { margin-top: 1.5rem; }
        .btn { background: #fff; color: #10182a; border: none; border-radius: 6px; padding: 0.75rem 2rem; font-size: 1.1rem; cursor: pointer; margin: 0 0.5rem; transition: background 0.2s, color 0.2s; text-decoration: none; display: inline-block; }
        .btn.stop { background: #e74c3c; color: #fff; }
        .btn:hover { background: #e0e6f7; color: #10182a; }
        .status { margin-top: 1rem; color: #fff; font-weight: bold; }
        .back-btn { margin-top: 1.5rem; }
    </style>
</head>
<body>
    <div class="modal">
        <div class="modal-content">
            <?php if ($song): ?>
                <img class="song-img" src="<?php echo htmlspecialchars($song['image']); ?>" alt="Song Image">
                <div class="song-title"><?php echo htmlspecialchars($song['title']); ?></div>
                <div class="song-artist">by <?php echo htmlspecialchars($song['artist_name']); ?> (<?php echo htmlspecialchars($song['country_name']); ?>)</div>
                <div class="song-album">Album: <?php echo htmlspecialchars($song['album_name']); ?></div>
                <div>Played at: <?php echo htmlspecialchars($song['playtime']); ?></div>
                <form method="post" class="controls">
                    <button class="btn" type="submit" name="play">Play</button>
                    <button class="btn stop" type="submit" name="stop">Stop</button>
                </form>
                <div class="status">Status: <?php echo htmlspecialchars($status); ?></div>
                <a href="homepage.php" class="btn back-btn">Back to Homepage</a>
            <?php else: ?>
                <div class="song-title">No music is currently playing.</div>
                <a href="homepage.php" class="btn back-btn">Back to Homepage</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 