<?php
// Database connection parameters
putenv('DB_USER');
putenv('MYSQL_USER');
putenv('USER');
$host = 'localhost';
$username = 'root';
$password = '';

// Function to clean up input files
function cleanupInputFiles() {
    $files = glob('InputFiles/*.txt');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}

// Function to ensure InputFiles directory exists
function ensureInputFilesDirectory() {
    if (!file_exists('InputFiles')) {
        mkdir('InputFiles', 0777, true);
    }
}

try {
    // Clean up existing files and ensure directory exists
    cleanupInputFiles();
    ensureInputFilesDirectory();
    
    // Run generate_input_files.php to create and populate input files
    include 'generate_input_files.php';
    
    // Create connection without database
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS efe_basbayram";
    $conn->exec($sql);
    
    // Select the database
    $conn->exec("USE efe_basbayram");
    
    // Create tables
    $conn->exec("CREATE TABLE IF NOT EXISTS COUNTRY (country_id INT PRIMARY KEY AUTO_INCREMENT, country_name VARCHAR(100) NOT NULL, country_code VARCHAR(2) NOT NULL)");
    $conn->exec("CREATE TABLE IF NOT EXISTS USERS (user_id INT PRIMARY KEY AUTO_INCREMENT, country_id INT, age INT, name VARCHAR(100) NOT NULL, username VARCHAR(50) NOT NULL UNIQUE, email VARCHAR(100) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, date_joined DATE, last_login DATETIME, follower_num INT DEFAULT 0, subscription_type VARCHAR(20), top_genre VARCHAR(50), num_songs_liked INT DEFAULT 0, most_played_artist VARCHAR(100), image VARCHAR(255), FOREIGN KEY (country_id) REFERENCES COUNTRY(country_id))");
    $conn->exec("CREATE TABLE IF NOT EXISTS ARTISTS (artist_id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(100) NOT NULL, genre VARCHAR(50), date_joined DATE, total_num_music INT DEFAULT 0, total_albums INT DEFAULT 0, listeners INT DEFAULT 0, bio TEXT, country_id INT, image VARCHAR(255), FOREIGN KEY (country_id) REFERENCES COUNTRY(country_id))");
    $conn->exec("CREATE TABLE IF NOT EXISTS ALBUMS (album_id INT PRIMARY KEY AUTO_INCREMENT, artist_id INT, name VARCHAR(100) NOT NULL, release_date DATE, genre VARCHAR(50), music_number INT DEFAULT 0, image VARCHAR(255), FOREIGN KEY (artist_id) REFERENCES ARTISTS(artist_id))");
    $conn->exec("CREATE TABLE IF NOT EXISTS SONGS (song_id INT PRIMARY KEY AUTO_INCREMENT, album_id INT, title VARCHAR(100) NOT NULL, duration INT, genre VARCHAR(50), release_date DATE, rank INT, image VARCHAR(255), FOREIGN KEY (album_id) REFERENCES ALBUMS(album_id))");
    $conn->exec("CREATE TABLE IF NOT EXISTS PLAYLISTS (playlist_id INT PRIMARY KEY AUTO_INCREMENT, user_id INT, title VARCHAR(100) NOT NULL, description TEXT, date_created DATE, image VARCHAR(255), FOREIGN KEY (user_id) REFERENCES USERS(user_id))");
    $conn->exec("CREATE TABLE IF NOT EXISTS PLAYLIST_SONGS (playlistsong_id INT PRIMARY KEY AUTO_INCREMENT, playlist_id INT, song_id INT, date_added DATE, FOREIGN KEY (playlist_id) REFERENCES PLAYLISTS(playlist_id), FOREIGN KEY (song_id) REFERENCES SONGS(song_id))");
    $conn->exec("CREATE TABLE IF NOT EXISTS PLAY_HISTORY (play_id INT PRIMARY KEY AUTO_INCREMENT, user_id INT, song_id INT, playtime DATETIME, FOREIGN KEY (user_id) REFERENCES USERS(user_id), FOREIGN KEY (song_id) REFERENCES SONGS(song_id))");
    
    // Run generate_data.php to create new input files and SQL file
    include 'generate_data.php';
    
    // Import data from the generated SQL file
    if (file_exists('database_setup.sql')) {
        $sql = file_get_contents('database_setup.sql');
        $conn->exec($sql);
    }
    
} catch(PDOException $e) {
    // Show error message in a simple HTML page
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title></head><body style="font-family:Arial,sans-serif;text-align:center;margin-top:100px;"><h2>Error: ' . htmlspecialchars($e->getMessage()) . '</h2></body></html>';
    exit();
}

// Close connection
$conn = null;

// Show message and redirect to login.html after 1.5 seconds
// (no JavaScript, just meta refresh)
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Database Initialized</title>
    <meta http-equiv="refresh" content="1.5;url=login.html">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f6fa; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .container { background: #fff; padding: 2rem 3rem; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); text-align: center; }
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid #4f8cff; border-radius: 50%; width: 32px; height: 32px; animation: spin 1s linear infinite; margin: 1rem auto; }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="loader"></div>
        <div>Database initialized! Redirecting to login...</div>
    </div>
</body>
</html>';
exit();
?> 