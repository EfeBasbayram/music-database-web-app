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
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $conn->prepare("SELECT name, country_id FROM USERS WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get all playlists
$stmt = $conn->prepare("SELECT * FROM PLAYLISTS WHERE user_id = ?");
$stmt->execute([$user_id]);
$playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get last 10 unique played songs for user
$songs = $conn->prepare("SELECT S.*, MAX(PH.playtime) AS playtime FROM PLAY_HISTORY PH JOIN SONGS S ON PH.song_id = S.song_id WHERE PH.user_id = ? GROUP BY PH.song_id ORDER BY playtime DESC LIMIT 10");
$songs->execute([$user_id]);
$lastPlayed = $songs->fetchAll(PDO::FETCH_ASSOC);

// Get 5 top artists from user's country
$artists = $conn->prepare("SELECT * FROM ARTISTS WHERE country_id = ? ORDER BY listeners DESC LIMIT 5");
$artists->execute([$user['country_id']]);
$topArtists = $artists->fetchAll(PDO::FETCH_ASSOC);

// Handle search and add playlist (simplified, no JS)
$searchMsg = '';
$playlistResults = [];
$songResults = [];
$artistResults = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['playlist_search'])) {
        $search = trim($_POST['playlist_search']);
        $pl = $conn->prepare("SELECT * FROM PLAYLISTS WHERE title LIKE ? COLLATE utf8mb4_general_ci AND user_id = ?");
        $pl->execute(['%' . $search . '%', $user_id]);
        $playlistResults = $pl->fetchAll(PDO::FETCH_ASSOC);
        if (!$playlistResults || count($playlistResults) === 0) {
            $searchMsg = 'Playlist not found in your playlists.';
        }
    } elseif (isset($_POST['song_search'])) {
        $search = trim($_POST['song_search']);
        $song = $conn->prepare(
            "SELECT S.* FROM PLAY_HISTORY PH
             JOIN SONGS S ON PH.song_id = S.song_id
             WHERE PH.user_id = ? AND S.title LIKE ? COLLATE utf8mb4_general_ci
             GROUP BY S.song_id
             ORDER BY MAX(PH.playtime) DESC"
        );
        $song->execute([$user_id, '%' . $search . '%']);
        $songResults = $song->fetchAll(PDO::FETCH_ASSOC);
        if (!$songResults || count($songResults) === 0) {
            $searchMsg = 'Song not found in your play history.';
        }
    } elseif (isset($_POST['artist_search'])) {
        $search = trim($_POST['artist_search']);
        $artist = $conn->prepare("SELECT * FROM ARTISTS WHERE name LIKE ? COLLATE utf8mb4_general_ci AND country_id = ?");
        $artist->execute(['%' . $search . '%', $user['country_id']]);
        $artistResults = $artist->fetchAll(PDO::FETCH_ASSOC);
        if (!$artistResults || count($artistResults) === 0) {
            $searchMsg = 'Artist not found in your country.';
        }
    } elseif (isset($_POST['add_playlist'])) {
        $title = trim($_POST['new_playlist_title']);
        $desc = trim($_POST['new_playlist_desc']);
        $img = 'https://picsum.photos/seed/playlist' . rand(1, 10000) . '/200/200';
        $date = date('Y-m-d');
        $stmt = $conn->prepare("INSERT INTO PLAYLISTS (user_id, title, description, date_created, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $desc, $date, $img]);
        header('Location: homepage.php');
        exit();
    } elseif (isset($_POST['history_song_search'])) {
        $search = trim($_POST['history_song_search']);
        $song = $conn->prepare(
            "SELECT S.* FROM PLAY_HISTORY PH
             JOIN SONGS S ON PH.song_id = S.song_id
             WHERE PH.user_id = ? AND S.title LIKE ? COLLATE utf8mb4_general_ci
             GROUP BY S.song_id
             ORDER BY MAX(PH.playtime) DESC"
        );
        $song->execute([$user_id, '%' . $search . '%']);
        $songResults = $song->fetchAll(PDO::FETCH_ASSOC);
        if (!$songResults || count($songResults) === 0) {
            $searchMsg = 'Song not found in your play history.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Music Player - Homepage</title>
    <style>
        body { font-family: Arial, sans-serif; background: #10182a; color: #fff; margin: 0; }
        .header { padding: 1rem 2rem; background: #16213e; border-bottom: 1px solid #16213e; }
        .main { display: flex; height: 90vh; }
        .sidebar { width: 320px; background: #16213e; border-right: 1px solid #16213e; padding: 1rem; display: flex; flex-direction: column; }
        .sidebar .search-row, .search-bar-form { display: flex; align-items: center; margin-bottom: 1rem; }
        .sidebar input[type="text"], .search-bar-form input[type="text"] {
            flex: 1;
            padding: 0.6rem 1rem;
            border: 1.5px solid #23395d;
            border-radius: 8px;
            background: #10182a;
            color: #fff;
            margin-right: 0.5rem;
            font-size: 1rem;
            transition: border 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(16,24,42,0.10);
        }
        .sidebar input[type="text"]:focus, .search-bar-form input[type="text"]:focus {
            border: 1.5px solid #fff;
            box-shadow: 0 0 0 2px #fff3;
        }
        .sidebar button, .sidebar .plus, .search-bar-form button {
            margin-left: 0.5rem;
            background: #fff;
            color: #10182a;
            border: none;
            border-radius: 8px;
            min-width: 80px;
            height: 40px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, transform 0.1s;
            box-shadow: 0 2px 8px rgba(16,24,42,0.10);
        }
        .sidebar button:hover, .sidebar .plus:hover, .search-bar-form button:hover {
            background: #e0e6f7;
            color: #10182a;
            transform: scale(1.04);
        }
        .playlist-list { flex: 1; overflow-y: auto; }
        .playlist-item { display: flex; align-items: center; margin-bottom: 1rem; background: #1a2238; border-radius: 12px; padding: 0.5rem; box-shadow: 0 2px 8px rgba(16,24,42,0.12); }
        .playlist-item img { width: 48px; height: 48px; border-radius: 10px; margin-right: 1rem; object-fit: cover; background: #23395d; }
        .playlist-item .info { font-size: 1rem; }
        .content { flex: 1; display: flex; flex-direction: column; padding: 1rem 2rem; }
        .top-section, .bottom-section { background: #1a2238; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 16px rgba(16,24,42,0.18); margin-bottom: 1.5rem; }
        .section-title { font-weight: bold; margin-bottom: 0.5rem; font-size: 1.2rem; color: #fff; }
        .song-list, .artist-list { margin: 0; padding: 0; list-style: none; }
        .song-list li, .artist-list li { display: flex; align-items: center; margin-bottom: 0.75rem; }
        .song-list .info, .artist-list .info { margin-left: 1rem; color: #e0e6f7; }
        .clickable-link, .song-list a, .artist-list a, .playlist-item .info a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            transition: background 0.18s, color 0.18s, transform 0.1s;
            display: inline-block;
            background: transparent;
        }
        .clickable-link:hover, .song-list a:hover, .artist-list a:hover, .playlist-item .info a:hover {
            background: #fff;
            color: #10182a;
            transform: scale(1.06);
            text-decoration: none;
        }
        .search-bar-form { margin-bottom: 0.5rem; }
        .error-msg { color: #e74c3c; margin-bottom: 0.5rem; }
        ::-webkit-scrollbar { width: 8px; background: #16213e; }
        ::-webkit-scrollbar-thumb { background: #23395d; border-radius: 8px; }
        input, button { outline: none; }
    </style>
</head>
<body>
    <div class="header">
        <h3>Hello, <?php echo htmlspecialchars($user['name']); ?>!</h3>
        <a href="generalSQL.html" class="clickable-link" style="float: right; margin-top: -30px;">Analytics Dashboard</a>
    </div>
    <div class="main">
        <div class="sidebar">
            <form class="search-row" method="post" style="margin-bottom:1rem;">
                <input type="text" name="playlist_search" placeholder="Search Playlist">
                <button type="submit" name="playlist_search_btn" title="Search Playlist">Search</button>
            </form>
            <?php if (isset($_POST['playlist_search'])): ?>
                <div class="playlist-list">
                    <?php if (!empty($playlistResults)): ?>
                        <?php foreach ($playlistResults as $pl): ?>
                            <div class="playlist-item">
                                <img src="<?= htmlspecialchars($pl['image']) ?>" alt="Playlist Image">
                                <div class="info">
                                    <a href="playlist.php?id=<?= htmlspecialchars($pl['playlist_id']) ?>" class="clickable-link"><?= htmlspecialchars($pl['title']) ?></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif ($searchMsg): ?>
                        <div class="error-msg"><?= htmlspecialchars($searchMsg) ?></div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="playlist-list">
                    <?php foreach ($playlists as $pl): ?>
                        <div class="playlist-item">
                            <img src="<?php echo htmlspecialchars($pl['image']); ?>" alt="Playlist Image">
                            <div class="info">
                                <a href="playlist.php?id=<?php echo $pl['playlist_id']; ?>" class="clickable-link"><?php echo htmlspecialchars($pl['title']); ?></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="post" style="margin-bottom:1rem;">
                <input type="text" name="new_playlist_title" placeholder="New Playlist Title" required>
                <input type="text" name="new_playlist_desc" placeholder="Description" required>
                <button type="submit" name="add_playlist">Add</button>
            </form>
        </div>
        <div class="content">
            <div class="top-section">
                <div class="section-title">Last 10 Played Songs</div>
                <form class="search-bar-form" method="post">
                    <input type="text" name="history_song_search" placeholder="Search Song in History">
                    <button type="submit">Search</button>
                </form>
                <?php if (isset($_POST['history_song_search'])): ?>
                    <ul class="song-list">
                        <?php if (!empty($songResults)): ?>
                            <?php foreach ($songResults as $song): ?>
                                <li>
                                    <img src="<?= htmlspecialchars($song['image']) ?>" alt="Song Image" style="width:32px;height:32px;border-radius:6px;object-fit:cover;margin-right:0.5rem;">
                                    <div style="display:inline-block;vertical-align:middle;">
                                        <a href="song.php?id=<?= htmlspecialchars($song['song_id']) ?>" class="clickable-link"><?= htmlspecialchars($song['title']) ?></a>
                                    </div>
                                    <?php if (!empty($song['playtime'])): ?>
                                        <div class="info">Played at: <?= htmlspecialchars($song['playtime']) ?></div>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php elseif ($searchMsg): ?>
                            <div class="error-msg"><?= htmlspecialchars($searchMsg) ?></div>
                        <?php endif; ?>
                    </ul>
                <?php else: ?>
                    <ul class="song-list">
                        <?php foreach ($lastPlayed as $song): ?>
                            <li>
                                <img src="<?php echo htmlspecialchars($song['image']); ?>" alt="Song Image" style="width:32px;height:32px;border-radius:6px;object-fit:cover;margin-right:0.5rem;">
                                <div style="display:inline-block;vertical-align:middle;"><a href="song.php?id=<?php echo $song['song_id']; ?>" class="clickable-link"><?php echo htmlspecialchars($song['title']); ?></a></div>
                                <div class="info">Played at: <?php echo htmlspecialchars($song['playtime']); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="bottom-section">
                <div class="section-title">Top 5 Artists from Your Country</div>
                <form class="search-bar-form" method="post">
                    <input type="text" name="artist_search" placeholder="Search Artist">
                    <button type="submit">Search</button>
                </form>
                <?php if (isset($_POST['artist_search'])): ?>
                    <ul class="artist-list">
                        <?php if (!empty($artistResults)): ?>
                            <?php foreach ($artistResults as $artist): ?>
                                <li>
                                    <img src="<?= htmlspecialchars($artist['image']) ?>" alt="Artist Image" width="48" height="48" style="border-radius:8px;object-fit:cover;">
                                    <div class="info">
                                        <a href="artistpage.php?id=<?= htmlspecialchars($artist['artist_id']) ?>" class="clickable-link"><?= htmlspecialchars($artist['name']) ?></a><br>
                                        Listeners: <?= htmlspecialchars($artist['listeners']) ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php elseif ($searchMsg): ?>
                            <div class="error-msg"><?= htmlspecialchars($searchMsg) ?></div>
                        <?php endif; ?>
                    </ul>
                <?php else: ?>
                    <ul class="artist-list">
                        <?php foreach ($topArtists as $artist): ?>
                            <li>
                                <img src="<?php echo htmlspecialchars($artist['image']); ?>" alt="Artist Image" width="48" height="48" style="border-radius:8px;object-fit:cover;">
                                <div class="info">
                                    <a href="artistpage.php?id=<?php echo $artist['artist_id']; ?>" class="clickable-link"><?php echo htmlspecialchars($artist['name']); ?></a><br>
                                    Listeners: <?php echo htmlspecialchars($artist['listeners']); ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php if ($searchMsg): ?>
                <div class="error-msg"><?php echo htmlspecialchars($searchMsg); ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 