<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'efe_basbayram';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get album ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid album ID.');
}
$album_id = (int)$_GET['id'];

// Fetch album details
$stmt = $conn->prepare("SELECT A.*, AR.name AS artist_name, AR.image AS artist_image FROM ALBUMS A JOIN ARTISTS AR ON A.artist_id = AR.artist_id WHERE A.album_id = ?");
$stmt->execute([$album_id]);
$album = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$album) {
    die('Album not found.');
}

// Fetch songs in this album
$stmt = $conn->prepare("SELECT * FROM SONGS WHERE album_id = ? ORDER BY title ASC");
$stmt->execute([$album_id]);
$songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($album['name']); ?> - Album</title>
    <style>
        body { font-family: Arial, sans-serif; background: #10182a; color: #fff; margin: 0; }
        .header { padding: 1rem 2rem; background: #16213e; border-bottom: 1px solid #16213e; }
        .container { max-width: 800px; margin: 2rem auto; background: #1a2238; border-radius: 16px; box-shadow: 0 2px 16px rgba(16,24,42,0.18); padding: 2rem; }
        .album-info { display: flex; align-items: center; margin-bottom: 2rem; }
        .album-cover { width: 120px; height: 120px; border-radius: 12px; object-fit: cover; margin-right: 2rem; background: #23395d; }
        .album-details { flex: 1; }
        .album-title { font-size: 2rem; font-weight: bold; color: #fff; margin-bottom: 0.5rem; }
        .artist-link { color: #fff; text-decoration: none; font-weight: bold; border-radius: 8px; padding: 0.25rem 0.5rem; background: transparent; transition: background 0.18s, color 0.18s, transform 0.1s; }
        .artist-link:hover { background: #fff; color: #10182a; transform: scale(1.06); text-decoration: none; }
        .song-list { list-style: none; padding: 0; margin: 0; }
        .song-item { display: flex; align-items: center; margin-bottom: 1rem; background: #23395d; border-radius: 10px; padding: 0.75rem 1rem; box-shadow: 0 2px 8px rgba(16,24,42,0.10); }
        .song-cover { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; margin-right: 1rem; background: #16213e; }
        .song-title { font-size: 1.1rem; color: #fff; font-weight: bold; margin-right: 1rem; }
        .song-link { color: #fff; text-decoration: none; border-radius: 8px; padding: 0.25rem 0.5rem; background: transparent; transition: background 0.18s, color 0.18s, transform 0.1s; }
        .song-link:hover { background: #fff; color: #10182a; transform: scale(1.06); text-decoration: none; }
        .no-songs { color: #e0e6f7; font-style: italic; }
        .back-link { display: inline-block; margin-bottom: 1.5rem; color: #4cc9f0; text-decoration: none; font-weight: bold; border-radius: 8px; padding: 0.25rem 0.75rem; background: #16213e; transition: background 0.18s, color 0.18s, transform 0.1s; }
        .back-link:hover { background: #fff; color: #10182a; transform: scale(1.06); text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <a href="homepage.php" class="back-link">← Back to Homepage</a>
    </div>
    <div class="container">
        <div class="album-info">
            <img src="<?php echo htmlspecialchars($album['image']); ?>" alt="Album Cover" class="album-cover">
            <div class="album-details">
                <div class="album-title"><?php echo htmlspecialchars($album['name']); ?></div>
                <div>by <a href="artistpage.php?id=<?php echo $album['artist_id']; ?>" class="artist-link">
                    <img src="<?php echo htmlspecialchars($album['artist_image']); ?>" alt="Artist Image" style="width:28px;height:28px;border-radius:50%;vertical-align:middle;margin-right:0.5rem;object-fit:cover;">
                    <?php echo htmlspecialchars($album['artist_name']); ?>
                </a></div>
                <div style="margin-top:0.5rem;color:#e0e6f7;">Released: <?php echo htmlspecialchars($album['release_date']); ?></div>
            </div>
        </div>
        <h2 style="margin-bottom:1rem;">Songs in this Album</h2>
        <?php if (count($songs) > 0): ?>
            <ul class="song-list">
                <?php foreach ($songs as $song): ?>
                    <li class="song-item">
                        <img src="<?php echo htmlspecialchars($song['image']); ?>" alt="Song Cover" class="song-cover">
                        <span class="song-title">
                            <a href="song.php?id=<?php echo $song['song_id']; ?>" class="song-link"><?php echo htmlspecialchars($song['title']); ?></a>
                        </span>
                        <span style="color:#e0e6f7;">Duration: <?php echo htmlspecialchars($song['duration']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="no-songs">No songs found in this album.</div>
        <?php endif; ?>
    </div>
</body>
</html> 