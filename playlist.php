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

$playlist_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($playlist_id <= 0) {
    echo "Invalid playlist.";
    exit();
}

// Get playlist info
$stmt = $conn->prepare("SELECT * FROM PLAYLISTS WHERE playlist_id = ?");
$stmt->execute([$playlist_id]);
$playlist = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$playlist) {
    echo "Playlist not found.";
    exit();
}

// Handle song search and add
$searchResults = [];
$addMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['song_search'])) {
        $search = trim($_POST['song_search']);
        $stmt = $conn->prepare("SELECT S.*, A.artist_id, A.name AS artist_name, C.country_name FROM SONGS S JOIN ALBUMS AL ON S.album_id = AL.album_id JOIN ARTISTS A ON AL.artist_id = A.artist_id JOIN COUNTRY C ON A.country_id = C.country_id WHERE LOWER(S.title) LIKE LOWER(?)");
        $stmt->execute(['%' . $search . '%']);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (isset($_POST['add_song_id'])) {
        $song_id = intval($_POST['add_song_id']);
        // Check if already in playlist
        $check = $conn->prepare("SELECT * FROM PLAYLIST_SONGS WHERE playlist_id = ? AND song_id = ?");
        $check->execute([$playlist_id, $song_id]);
        if (!$check->fetch()) {
            $date = date('Y-m-d');
            $add = $conn->prepare("INSERT INTO PLAYLIST_SONGS (playlist_id, song_id, date_added) VALUES (?, ?, ?)");
            $add->execute([$playlist_id, $song_id, $date]);
            $addMsg = 'Song added to playlist!';
        } else {
            $addMsg = 'Song already in playlist!';
        }
    }
}

// Get all songs in the playlist with artist and country
$songs = $conn->prepare("SELECT S.*, A.name AS artist_name, C.country_name FROM PLAYLIST_SONGS PS JOIN SONGS S ON PS.song_id = S.song_id JOIN ALBUMS AL ON S.album_id = AL.album_id JOIN ARTISTS A ON AL.artist_id = A.artist_id JOIN COUNTRY C ON A.country_id = C.country_id WHERE PS.playlist_id = ?");
$songs->execute([$playlist_id]);
$playlistSongs = $songs->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Playlist: <?php echo htmlspecialchars($playlist['title']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #10182a; color: #fff; margin: 0; }
        .container { max-width: 800px; margin: 2rem auto; background: #1a2238; border-radius: 16px; box-shadow: 0 2px 16px rgba(16,24,42,0.18); padding: 2.5rem; }
        h2 { margin-top: 0; color: #fff; }
        .song-list { list-style: none; padding: 0; }
        .song-list li { display: flex; align-items: center; border-bottom: 1px solid #23395d; padding: 0.75rem 0; }
        .song-info { display: flex; flex-direction: column; }
        .search-bar-form { margin-bottom: 1.5rem; display: flex; }
        .search-bar-form input[type="text"] { flex: 1; padding: 0.5rem; border: 1.5px solid #23395d; border-radius: 6px; background: #10182a; color: #fff; margin-right: 0.5rem; }
        .search-bar-form input[type="text"]:focus { border: 1.5px solid #fff; box-shadow: 0 0 0 2px #fff3; }
        .search-bar-form button { background: #fff; color: #10182a; border: none; border-radius: 6px; padding: 0.5rem 1.5rem; cursor: pointer; font-size: 1rem; transition: background 0.2s, color 0.2s; }
        .search-bar-form button:hover { background: #e0e6f7; color: #10182a; }
        .msg { color: #fff; margin-bottom: 1rem; }
        .search-result { background: #10182a; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; color: #fff; box-shadow: 0 2px 8px rgba(16,24,42,0.12); }
        .add-btn { background: #fff; color: #10182a; border: none; border-radius: 6px; padding: 0.5rem 1rem; cursor: pointer; margin-left: 1rem; transition: background 0.2s, color 0.2s; }
        .add-btn:hover { background: #e0e6f7; color: #10182a; }
        .playlist-img { width: 120px; height: 120px; border-radius: 16px; object-fit: cover; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(16,24,42,0.18); background: #23395d; }
        strong a { color: #fff; text-decoration: none; }
        strong a:hover { text-decoration: underline; background: #fff; color: #10182a; }
        .song-list img { width: 32px; height: 32px; border-radius: 6px; object-fit: cover; margin-right: 0.5rem; background: #23395d; }
        .song-list span { color: #e0e6f7; }
        .back-btn { background: #fff; color: #10182a; border: none; border-radius: 6px; padding: 0.75rem 2rem; font-size: 1.1rem; cursor: pointer; margin-top: 1rem; transition: background 0.2s, color 0.2s; text-decoration: none; display: inline-block; }
        .back-btn:hover { background: #e0e6f7; color: #10182a; }
        @media (max-width: 600px) { .container { padding: 1rem; } }
    </style>
</head>
<body>
    <div class="container">
        <img src="<?php echo htmlspecialchars($playlist['image']); ?>" alt="Playlist Image" style="width:120px;height:120px;border-radius:16px;object-fit:cover;margin-bottom:1.5rem;box-shadow:0 2px 8px rgba(16,24,42,0.18);background:#23395d;">
        <h2>Playlist: <?php echo htmlspecialchars($playlist['title']); ?></h2>
        <p><?php echo htmlspecialchars($playlist['description']); ?></p>
        <form class="search-bar-form" method="post">
            <input type="text" name="song_search" placeholder="Search Song by Title" required>
            <button type="submit">Search</button>
        </form>
        <?php if ($addMsg): ?>
            <div class="msg"><?php echo htmlspecialchars($addMsg); ?></div>
        <?php endif; ?>
        <?php if (isset($_POST['song_search'])): ?>
            <div class="search-result">
                <?php if (count($searchResults) > 0): ?>
                    <?php foreach ($searchResults as $result): ?>
                        <div style="margin-bottom:0.5rem;">
                            <strong><?php echo htmlspecialchars($result['title']); ?></strong> by <?php echo htmlspecialchars($result['artist_name']); ?> (<?php echo htmlspecialchars($result['country_name']); ?>)
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="add_song_id" value="<?php echo $result['song_id']; ?>">
                                <button class="add-btn" type="submit">Add to Playlist</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    Song '<?php echo htmlspecialchars($_POST['song_search']); ?>' not found.
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <h3>Songs in this Playlist</h3>
        <ul class="song-list">
            <?php foreach ($playlistSongs as $song): ?>
                <li>
                    <img src="<?php echo htmlspecialchars($song['image']); ?>" alt="Song Image" style="width:32px;height:32px;border-radius:6px;object-fit:cover;margin-right:0.5rem;background:#23395d;">
                    <span style="display:inline-block;vertical-align:middle;"><strong><a href="song.php?id=<?php echo $song['song_id']; ?>" style="color: #fff; text-decoration: none;"><?php echo htmlspecialchars($song['title']); ?></a></strong></span>
                    <span style="color: #e0e6f7;">by <?php echo htmlspecialchars($song['artist_name']); ?> (<?php echo htmlspecialchars($song['country_name']); ?>)</span>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="homepage.php" class="back-btn">Back to Homepage</a>
    </div>
</body>
</html> 