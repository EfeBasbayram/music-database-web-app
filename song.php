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
$song_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($song_id <= 0) {
    echo "Invalid song.";
    exit();
}

// Get song info with artist and album
$stmt = $conn->prepare("SELECT S.*, AL.name AS album_name, A.name AS artist_name, C.country_name FROM SONGS S JOIN ALBUMS AL ON S.album_id = AL.album_id JOIN ARTISTS A ON AL.artist_id = A.artist_id JOIN COUNTRY C ON A.country_id = C.country_id WHERE S.song_id = ?");
$stmt->execute([$song_id]);
$song = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$song) {
    echo "Song not found.";
    exit();
}

$playMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['play_song'])) {
    $now = date('Y-m-d H:i:s');
    $add = $conn->prepare("INSERT INTO PLAY_HISTORY (user_id, song_id, playtime) VALUES (?, ?, ?)");
    $add->execute([$user_id, $song_id, $now]);
    header('Location: currentmusic.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($song['title']); ?> - Song Page</title>
    <style>
        body { font-family: Arial, sans-serif; background: #10182a; color: #fff; margin: 0; }
        .container { max-width: 600px; margin: 2rem auto; background: #1a2238; border-radius: 16px; box-shadow: 0 2px 16px rgba(16,24,42,0.18); padding: 2.5rem; text-align: center; }
        h2 { margin-top: 0; color: #fff; }
        .msg { color: #fff; margin-bottom: 1rem; }
        .play-btn, .back-btn { background: #fff; color: #10182a; border: none; border-radius: 6px; padding: 0.75rem 2rem; font-size: 1.1rem; cursor: pointer; margin: 1rem 0.5rem; transition: background 0.2s, color 0.2s; }
        .play-btn:hover, .back-btn:hover { background: #e0e6f7; color: #10182a; }
        .song-img { width: 120px; height: 120px; border-radius: 16px; object-fit: cover; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(16,24,42,0.18); background: #23395d; }
        p { color: #e0e6f7; }
        .button-group { margin-top: 1.5rem; }
        @media (max-width: 600px) { .container { padding: 1rem; } }
    </style>
</head>
<body>
    <div class="container">
        <img src="<?php echo htmlspecialchars($song['image']); ?>" alt="Song Image" class="song-img">
        <h2><?php echo htmlspecialchars($song['title']); ?></h2>
        <p><strong>Artist:</strong> <?php echo htmlspecialchars($song['artist_name']); ?> (<?php echo htmlspecialchars($song['country_name']); ?>)</p>
        <p><strong>Album:</strong> <a href="album.php?id=<?php echo $song['album_id']; ?>" style="color:#4cc9f0;text-decoration:none;font-weight:bold;"><?php echo htmlspecialchars($song['album_name']); ?></a></p>
        <p><strong>Genre:</strong> <?php echo htmlspecialchars($song['genre']); ?></p>
        <p><strong>Duration:</strong> <?php echo htmlspecialchars($song['duration']); ?> seconds</p>
        <p><strong>Release Date:</strong> <?php echo htmlspecialchars($song['release_date']); ?></p>
        <?php if ($playMsg): ?>
            <div class="msg"><?php echo htmlspecialchars($playMsg); ?></div>
        <?php endif; ?>
        <div class="button-group">
            <form method="post" style="display: inline;">
                <button class="play-btn" type="submit" name="play_song">Play</button>
            </form>
            <a href="homepage.php" class="back-btn" style="text-decoration: none; display: inline-block;">Back to Homepage</a>
        </div>
    </div>
</body>
</html> 