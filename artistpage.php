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

$artist_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($artist_id <= 0) {
    echo "Invalid artist.";
    exit();
}

// Get artist info
$stmt = $conn->prepare("SELECT A.*, C.country_name FROM ARTISTS A JOIN COUNTRY C ON A.country_id = C.country_id WHERE artist_id = ?");
$stmt->execute([$artist_id]);
$artist = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$artist) {
    echo "Artist not found.";
    exit();
}

// Get last 5 albums
$albums = $conn->prepare("SELECT * FROM ALBUMS WHERE artist_id = ? ORDER BY release_date DESC LIMIT 5");
$albums->execute([$artist_id]);
$lastAlbums = $albums->fetchAll(PDO::FETCH_ASSOC);

// Get top 5 most listened songs (by play count)
$songs = $conn->prepare("SELECT S.*, COUNT(PH.play_id) AS play_count FROM SONGS S JOIN ALBUMS AL ON S.album_id = AL.album_id LEFT JOIN PLAY_HISTORY PH ON S.song_id = PH.song_id WHERE AL.artist_id = ? GROUP BY S.song_id ORDER BY play_count DESC LIMIT 5");
$songs->execute([$artist_id]);
$topSongs = $songs->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($artist['name']); ?> - Artist Page</title>
    <style>
        body { font-family: Arial, sans-serif; background: #10182a; color: #fff; margin: 0; }
        .main { display: flex; max-width: 1100px; margin: 2rem auto; background: #1a2238; border-radius: 16px; box-shadow: 0 2px 16px rgba(16,24,42,0.18); }
        .sidebar { width: 320px; padding: 2rem; border-right: 1px solid #1a2238; display: flex; flex-direction: column; align-items: center; background: #16213e; }
        .sidebar img { width: 160px; height: 160px; border-radius: 16px; object-fit: cover; margin-bottom: 1rem; box-shadow: 0 2px 8px rgba(16,24,42,0.18); background: #23395d; }
        .sidebar .info { text-align: center; }
        .sidebar h2 { color: #fff; }
        .content { flex: 1; padding: 2rem; display: flex; flex-direction: column; }
        .section { background: #10182a; border-radius: 12px; margin-bottom: 2rem; padding: 1.5rem 2rem; box-shadow: 0 2px 8px rgba(16,24,42,0.12); }
        .section-title { font-weight: bold; font-size: 1.2rem; margin-bottom: 1rem; color: #fff; }
        .album-list, .song-list { list-style: none; padding: 0; margin: 0; }
        .album-list li, .song-list li { margin-bottom: 0.75rem; }
        .album-link, .song-link { color: #fff; text-decoration: none; font-weight: bold; border-radius: 8px; padding: 0.25rem 0.5rem; transition: background 0.18s, color 0.18s, transform 0.1s; display: inline-block; }
        .album-link:hover, .song-link:hover { background: #fff; color: #10182a; transform: scale(1.06); text-decoration: none; }
        .empty-msg { color: #e0e6f7; font-style: italic; }
        p { color: #e0e6f7; }
        .back-btn { background: #fff; color: #10182a; border: none; border-radius: 6px; padding: 0.75rem 2rem; font-size: 1.1rem; cursor: pointer; margin-top: 1rem; transition: background 0.2s, color 0.2s; text-decoration: none; display: inline-block; }
        .back-btn:hover { background: #e0e6f7; color: #10182a; }
        @media (max-width: 800px) { .main { flex-direction: column; } .sidebar { width: 100%; border-right: none; border-bottom: 1px solid #1a2238; } }
    </style>
</head>
<body>
    <div class="main">
        <div class="sidebar">
            <img src="<?php echo htmlspecialchars($artist['image']); ?>" alt="Artist Image">
            <div class="info">
                <h2><?php echo htmlspecialchars($artist['name']); ?></h2>
                <p><strong>Country:</strong> <?php echo htmlspecialchars($artist['country_name']); ?></p>
                <p><strong>Genre:</strong> <?php echo htmlspecialchars($artist['genre']); ?></p>
                <p><strong>Listeners:</strong> <?php echo htmlspecialchars($artist['listeners']); ?></p>
                <p><strong>Total Albums:</strong> <?php echo htmlspecialchars($artist['total_albums']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($artist['bio'])); ?></p>
                <a href="homepage.php" class="back-btn">Back to Homepage</a>
            </div>
        </div>
        <div class="content">
            <div class="section">
                <div class="section-title">Last 5 Albums</div>
                <ul class="album-list">
                    <?php if (count($lastAlbums) > 0): ?>
                        <?php foreach ($lastAlbums as $album): ?>
                            <li><a class="album-link" href="album.php?id=<?php echo $album['album_id']; ?>"><?php echo htmlspecialchars($album['name']); ?></a> (<?php echo htmlspecialchars($album['release_date']); ?>)</li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="empty-msg">No albums found for this artist.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="section">
                <div class="section-title">Top 5 Most Listened Songs</div>
                <ul class="song-list">
                    <?php if (count($topSongs) > 0): ?>
                        <?php foreach ($topSongs as $song): ?>
                            <li><a class="song-link" href="song.php?id=<?php echo $song['song_id']; ?>"><?php echo htmlspecialchars($song['title']); ?></a> (<?php echo $song['play_count']; ?> plays)</li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="empty-msg">No songs found for this artist.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html> 