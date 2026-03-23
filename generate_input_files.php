<?php
// Function to generate random date between two dates
function randomDateInputFiles($start_date, $end_date) {
    $start = strtotime($start_date);
    $end = strtotime($end_date);
    $timestamp = mt_rand($start, $end);
    return date('Y-m-d', $timestamp);
}

// Function to generate random datetime
function randomDateTimeInputFiles($start_date, $end_date) {
    $start = strtotime($start_date);
    $end = strtotime($end_date);
    $timestamp = mt_rand($start, $end);
    return date('Y-m-d H:i:s', $timestamp);
}

// Function to generate random name
function randomNameInputFiles() {
    $firstNames = ['John', 'Jane', 'Mike', 'Sarah', 'David', 'Emma', 'James', 'Lisa', 'Tom', 'Anna', 'Chris', 'Mary', 'Robert', 'Patricia', 'Michael', 'Jennifer', 'William', 'Linda', 'Richard', 'Barbara'];
    $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin'];
    return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
}

// Function to generate random username
function randomUsernameInputFiles($name) {
    $name = strtolower(str_replace(' ', '', $name));
    return $name . rand(1, 999);
}

// Function to generate random email
function randomEmailInputFiles($userNameInput) {
    $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com'];
    return $userNameInput . '@' . $domains[array_rand($domains)];
}

// Function to generate random genre
function randomGenreInputFiles() {
    $genres = ['Rock', 'Pop', 'Hip Hop', 'Electronic', 'Jazz', 'K-Pop', 'R&B', 'Country', 'Classical', 'Metal', 'Folk', 'Blues', 'Reggae', 'Soul', 'Funk'];
    return $genres[array_rand($genres)];
}

// Generate Users (100)
$users = [];
for ($i = 1; $i <= 100; $i++) {
    $name = randomNameInputFiles();
    $userNameInput = randomUsernameInputFiles($name);
    $users[] = sprintf("%d,%d,%s,%s,%s,password%d,%s,%s,%d,%s,%s,%d,%s,user%d.jpg",
        rand(1, 70), // country_id
        rand(18, 65), // age
        $name,
        $userNameInput,
        randomEmailInputFiles($userNameInput),
        $i,
        randomDateInputFiles('2020-01-01', '2024-03-15'),
        randomDateTimeInputFiles('2024-03-01', '2024-03-15'),
        rand(0, 1000),
        rand(0, 1) ? 'Premium' : 'Free',
        randomGenreInputFiles(),
        rand(0, 500),
        'Artist ' . rand(1, 100),
        $i
    );
}
file_put_contents('InputFiles/inputUsers.txt', implode("\n", $users));

// Generate Artists (100)
$artists = [];
for ($i = 1; $i <= 100; $i++) {
    $artists[] = sprintf("Artist %d,%s,%s,%d,%d,%d,%s,%d,artist%d.jpg",
        $i,
        randomGenreInputFiles(),
        randomDateInputFiles('1960-01-01', '2020-01-01'),
        rand(50, 300),
        rand(5, 20),
        rand(100000, 2000000),
        'Bio for Artist ' . $i,
        rand(1, 70),
        $i
    );
}
file_put_contents('InputFiles/inputArtists.txt', implode("\n", $artists));

// Generate Albums (200)
$albums = [];
for ($i = 1; $i <= 200; $i++) {
    $albums[] = sprintf("%d,Album %d,%s,%s,%d,album%d.jpg",
        rand(1, 100), // artist_id
        $i,
        randomDateInputFiles('1960-01-01', '2024-03-15'),
        randomGenreInputFiles(),
        rand(8, 20),
        $i
    );
}
file_put_contents('InputFiles/inputAlbums.txt', implode("\n", $albums));

// Generate Songs (1000)
$songs = [];
for ($i = 1; $i <= 1000; $i++) {
    $songs[] = sprintf("%d,Song %d,%d,%s,%s,%d,song%d.jpg",
        rand(1, 200), // album_id
        $i,
        rand(120, 600), // duration in seconds
        randomGenreInputFiles(),
        randomDateInputFiles('1960-01-01', '2024-03-15'),
        rand(1, 100),
        $i
    );
}
file_put_contents('InputFiles/inputSongs.txt', implode("\n", $songs));

// Generate Playlists (500)
$playlists = [];
for ($i = 1; $i <= 500; $i++) {
    $playlists[] = sprintf("%d,Playlist %d,Description for playlist %d,%s,playlist%d.jpg",
        rand(1, 100), // user_id
        $i,
        $i,
        randomDateInputFiles('2020-01-01', '2024-03-15'),
        $i
    );
}
file_put_contents('InputFiles/inputPlaylists.txt', implode("\n", $playlists));

// Generate Playlist Songs (500)
$playlistSongs = [];
for ($i = 1; $i <= 500; $i++) {
    $playlistSongs[] = sprintf("%d,%d,%s",
        rand(1, 500), // playlist_id
        rand(1, 1000), // song_id
        randomDateInputFiles('2020-01-01', '2024-03-15')
    );
}
file_put_contents('InputFiles/inputPlaylistSongs.txt', implode("\n", $playlistSongs));

// Generate Play History (100)
$playHistory = [];
for ($i = 1; $i <= 100; $i++) {
    $playHistory[] = sprintf("%d,%d,%s",
        rand(1, 100), // user_id
        rand(1, 1000), // song_id
        randomDateTimeInputFiles('2024-03-01', '2024-03-15')
    );
}
file_put_contents('InputFiles/inputPlayHistory.txt', implode("\n", $playHistory));

// Generate first_names.txt
$firstNames = ['John', 'Jane', 'Mike', 'Sarah', 'David', 'Emma', 'James', 'Lisa', 'Tom', 'Anna', 'Chris', 'Mary', 'Robert', 'Patricia', 'Michael', 'Jennifer', 'William', 'Linda', 'Richard', 'Barbara'];
file_put_contents('InputFiles/first_names.txt', implode("\n", $firstNames));

// Generate last_names.txt
$lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin'];
file_put_contents('InputFiles/last_names.txt', implode("\n", $lastNames));

// Generate countries.txt
$countries = ['Turkey', 'United States', 'Germany', 'France', 'Italy', 'Spain', 'United Kingdom', 'Canada', 'Australia', 'Brazil', 'Japan', 'China', 'Russia', 'India', 'Mexico', 'Netherlands', 'Sweden', 'Norway', 'Denmark', 'Finland'];
file_put_contents('InputFiles/countries.txt', implode("\n", $countries));

// Generate genres.txt
$genres = ['Pop', 'Rock', 'Hip Hop', 'Jazz', 'Classical', 'Electronic', 'R&B', 'Country', 'Metal', 'Folk', 'Blues', 'Reggae', 'Soul', 'Funk', 'K-Pop'];
file_put_contents('InputFiles/genres.txt', implode("\n", $genres));

echo "All input files have been generated in the InputFiles folder!\n";
?> 