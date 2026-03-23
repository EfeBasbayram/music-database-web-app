<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "efe_basbayram";

// Helper: Read all countries (ISO 3166-1 alpha-2 codes)
$countriesList = [
    ["Afghanistan", "AF"], ["Albania", "AL"], ["Algeria", "DZ"], ["Andorra", "AD"], ["Angola", "AO"], ["Antigua and Barbuda", "AG"], ["Argentina", "AR"], ["Armenia", "AM"], ["Australia", "AU"], ["Austria", "AT"], ["Azerbaijan", "AZ"], ["Bahamas", "BS"], ["Bahrain", "BH"], ["Bangladesh", "BD"], ["Barbados", "BB"], ["Belarus", "BY"], ["Belgium", "BE"], ["Belize", "BZ"], ["Benin", "BJ"], ["Bhutan", "BT"], ["Bolivia", "BO"], ["Bosnia and Herzegovina", "BA"], ["Botswana", "BW"], ["Brazil", "BR"], ["Brunei", "BN"], ["Bulgaria", "BG"], ["Burkina Faso", "BF"], ["Burundi", "BI"], ["Cabo Verde", "CV"], ["Cambodia", "KH"], ["Cameroon", "CM"], ["Canada", "CA"], ["Central African Republic", "CF"], ["Chad", "TD"], ["Chile", "CL"], ["China", "CN"], ["Colombia", "CO"], ["Comoros", "KM"], ["Congo", "CG"], ["Costa Rica", "CR"], ["Croatia", "HR"], ["Cuba", "CU"], ["Cyprus", "CY"], ["Czechia", "CZ"], ["Denmark", "DK"], ["Djibouti", "DJ"], ["Dominica", "DM"], ["Dominican Republic", "DO"], ["Ecuador", "EC"], ["Egypt", "EG"], ["El Salvador", "SV"], ["Equatorial Guinea", "GQ"], ["Eritrea", "ER"], ["Estonia", "EE"], ["Eswatini", "SZ"], ["Ethiopia", "ET"], ["Fiji", "FJ"], ["Finland", "FI"], ["France", "FR"], ["Gabon", "GA"], ["Gambia", "GM"], ["Georgia", "GE"], ["Germany", "DE"], ["Ghana", "GH"], ["Greece", "GR"], ["Grenada", "GD"], ["Guatemala", "GT"], ["Guinea", "GN"], ["Guinea-Bissau", "GW"], ["Guyana", "GY"], ["Haiti", "HT"], ["Honduras", "HN"], ["Hungary", "HU"], ["Iceland", "IS"], ["India", "IN"], ["Indonesia", "ID"], ["Iran", "IR"], ["Iraq", "IQ"], ["Ireland", "IE"], ["Israel", "IL"], ["Italy", "IT"], ["Jamaica", "JM"], ["Japan", "JP"], ["Jordan", "JO"], ["Kazakhstan", "KZ"], ["Kenya", "KE"], ["Kiribati", "KI"], ["Kuwait", "KW"], ["Kyrgyzstan", "KG"], ["Laos", "LA"], ["Latvia", "LV"], ["Lebanon", "LB"], ["Lesotho", "LS"], ["Liberia", "LR"], ["Libya", "LY"], ["Liechtenstein", "LI"], ["Lithuania", "LT"], ["Luxembourg", "LU"], ["Madagascar", "MG"], ["Malawi", "MW"], ["Malaysia", "MY"], ["Maldives", "MV"], ["Mali", "ML"], ["Malta", "MT"], ["Marshall Islands", "MH"], ["Mauritania", "MR"], ["Mauritius", "MU"], ["Mexico", "MX"], ["Micronesia", "FM"], ["Moldova", "MD"], ["Monaco", "MC"], ["Mongolia", "MN"], ["Montenegro", "ME"], ["Morocco", "MA"], ["Mozambique", "MZ"], ["Myanmar", "MM"], ["Namibia", "NA"], ["Nauru", "NR"], ["Nepal", "NP"], ["Netherlands", "NL"], ["New Zealand", "NZ"], ["Nicaragua", "NI"], ["Niger", "NE"], ["Nigeria", "NG"], ["North Korea", "KP"], ["North Macedonia", "MK"], ["Norway", "NO"], ["Oman", "OM"], ["Pakistan", "PK"], ["Palau", "PW"], ["Palestine", "PS"], ["Panama", "PA"], ["Papua New Guinea", "PG"], ["Paraguay", "PY"], ["Peru", "PE"], ["Philippines", "PH"], ["Poland", "PL"], ["Portugal", "PT"], ["Qatar", "QA"], ["Romania", "RO"], ["Russia", "RU"], ["Rwanda", "RW"], ["Saint Kitts and Nevis", "KN"], ["Saint Lucia", "LC"], ["Saint Vincent and the Grenadines", "VC"], ["Samoa", "WS"], ["San Marino", "SM"], ["Sao Tome and Principe", "ST"], ["Saudi Arabia", "SA"], ["Senegal", "SN"], ["Serbia", "RS"], ["Seychelles", "SC"], ["Sierra Leone", "SL"], ["Singapore", "SG"], ["Slovakia", "SK"], ["Slovenia", "SI"], ["Solomon Islands", "SB"], ["Somalia", "SO"], ["South Africa", "ZA"], ["South Korea", "KR"], ["South Sudan", "SS"], ["Spain", "ES"], ["Sri Lanka", "LK"], ["Sudan", "SD"], ["Suriname", "SR"], ["Sweden", "SE"], ["Switzerland", "CH"], ["Syria", "SY"], ["Taiwan", "TW"], ["Tajikistan", "TJ"], ["Tanzania", "TZ"], ["Thailand", "TH"], ["Timor-Leste", "TL"], ["Togo", "TG"], ["Tonga", "TO"], ["Trinidad and Tobago", "TT"], ["Tunisia", "TN"], ["Turkey", "TR"], ["Turkmenistan", "TM"], ["Tuvalu", "TV"], ["Uganda", "UG"], ["Ukraine", "UA"], ["United Arab Emirates", "AE"], ["United Kingdom", "GB"], ["United States", "US"], ["Uruguay", "UY"], ["Uzbekistan", "UZ"], ["Vanuatu", "VU"], ["Vatican City", "VA"], ["Venezuela", "VE"], ["Vietnam", "VN"], ["Yemen", "YE"], ["Zambia", "ZM"], ["Zimbabwe", "ZW"]
];

// Helper: Random data generators
function randomDate($start, $end) {
    return date('Y-m-d', rand(strtotime($start), strtotime($end)));
}
function randomDateTime($start, $end) {
    return date('Y-m-d H:i:s', rand(strtotime($start), strtotime($end)));
}
function randomFromArray($arr) {
    return $arr[array_rand($arr)];
}
function randomEmail($username) {
    $domains = ['gmail.com', 'yahoo.com', 'hotmail.com'];
    return $username . '@' . $domains[array_rand($domains)];
}
function randomImage($prefix, $id) {
    return strtolower($prefix) . $id . '.jpg';
}

// 1. COUNTRY
$countries = [];
$countryInserts = [];
foreach ($countriesList as $i => $c) {
    $countries[] = [
        'country_id' => $i+1,
        'country_name' => $c[0],
        'country_code' => $c[1]
    ];
    $countryInserts[] = "INSERT INTO COUNTRY (country_id, country_name, country_code) VALUES (".($i+1).", '".$c[0]."', '".$c[1]."');";
}

// 2. USERS
$firstNames = ['John','Jane','Mike','Sarah','David','Emma','James','Lisa','Tom','Anna','Chris','Mary','Robert','Patricia','Michael','Jennifer','William','Linda','Richard','Barbara'];
$lastNames = ['Smith','Johnson','Williams','Brown','Jones','Garcia','Miller','Davis','Rodriguez','Martinez','Hernandez','Lopez','Gonzalez','Wilson','Anderson','Thomas','Taylor','Moore','Jackson','Martin'];
$genres = ['Pop','Rock','Hip Hop','Jazz','Classical','Electronic','R&B','Country','Metal','Folk','Blues','Reggae','Soul','Funk','K-Pop'];
$users = [];
$userInserts = [];
for ($i=1; $i<=100; $i++) {
    $first = randomFromArray($firstNames);
    $last = randomFromArray($lastNames);
    $name = "$first $last";
    $username = strtolower($first.$last.rand(100,999));
    $email = randomEmail($username);
    $country_id = rand(1, count($countries));
    $age = rand(18, 70);
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $date_joined = randomDate('2020-01-01', '2023-12-31');
    $last_login = randomDateTime($date_joined, '2024-03-20');
    $follower_num = rand(0, 10000);
    $subscription_type = randomFromArray(['Free','Premium']);
    $top_genre = randomFromArray($genres);
    $num_songs_liked = rand(0, 1000);
    $most_played_artist = "Artist ".rand(1,100);
    $image = randomImage('user', $i);
    $users[] = [
        'country_id' => $country_id,
        'user_id' => $i,
        'age' => $age,
        'name' => $name,
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'date_joined' => $date_joined,
        'last_login' => $last_login,
        'follower_num' => $follower_num,
        'subscription_type' => $subscription_type,
        'top_genre' => $top_genre,
        'num_songs_liked' => $num_songs_liked,
        'most_played_artist' => $most_played_artist,
        'image' => $image
    ];
    $userInserts[] = "INSERT INTO USERS (country_id, user_id, age, name, username, email, password, date_joined, last_login, follower_num, subscription_type, top_genre, num_songs_liked, most_played_artist, image) VALUES ($country_id, $i, $age, '$name', '$username', '$email', '$password', '$date_joined', '$last_login', $follower_num, '$subscription_type', '$top_genre', $num_songs_liked, '$most_played_artist', '$image');";
}

// Sample artist and playlist names
$artistNames = [
    'The Wanderers', 'Blue Horizon', 'Echoes', 'Starlight', 'Neon Dreams', 'The Classics', 'Urban Groove', 'Sunset Drive', 'Night Owls', 'Electric Pulse',
    'Golden Era', 'The Dreamers', 'Silver Strings', 'Velvet Sound', 'The Nomads', 'Crystal Waves', 'The Mavericks', 'Silent Voices', 'The Rebels', 'Infinite Loop',
    'The Outliers', 'The Originals', 'The Visionaries', 'The Innovators', 'The Pioneers', 'The Legends', 'The Icons', 'The Virtuosos', 'The Prodigies', 'The Maestros',
    'The Harmonies', 'The Beats', 'The Melodies', 'The Rhythms', 'The Chords', 'The Notes', 'The Scales', 'The Keys', 'The Tones', 'The Vibes',
    'The Frequencies', 'The Waves', 'The Soundscapes', 'The Grooves', 'The Tunes', 'The Jams', 'The Sessions', 'The Mixers', 'The Blends', 'The Fusions',
    'The Classics', 'The Moderns', 'The Future', 'The Past', 'The Present', 'The Timeless', 'The Eternal', 'The Infinite', 'The Cosmic', 'The Solar',
    'The Lunar', 'The Stellar', 'The Galactic', 'The Universal', 'The Celestial', 'The Astral', 'The Nebula', 'The Comet', 'The Meteor', 'The Orbit',
    'The Eclipse', 'The Aurora', 'The Spectrum', 'The Prism', 'The Reflection', 'The Mirage', 'The Oasis', 'The Desert', 'The Forest', 'The River',
    'The Ocean', 'The Mountain', 'The Valley', 'The Canyon', 'The Peak', 'The Summit', 'The Ridge', 'The Cliff', 'The Plateau', 'The Plain',
    'The Field', 'The Meadow', 'The Grove', 'The Woods', 'The Jungle', 'The Island', 'The Coast', 'The Shore', 'The Bay', 'The Harbor'
];
$playlistNames = [
    'Chill Vibes', 'Workout Mix', 'Top Hits', 'Throwback', 'Party Time', 'Focus', 'Road Trip', 'Relaxation', 'Indie Essentials', 'Pop Parade',
    'Rock Anthems', 'Jazz Lounge', 'Classical Calm', 'Hip Hop Heat', 'Country Roads', 'Metal Madness', 'Soulful Sundays', 'Funk Factory', 'K-Pop Craze', 'Electronic Energy',
    'Morning Motivation', 'Evening Unwind', 'Late Night', 'Feel Good', 'Summer Hits', 'Winter Warmers', 'Spring Fresh', 'Autumn Leaves', 'Festival Fever', 'Love Songs',
    'Breakup Ballads', 'Dance Floor', 'Study Session', 'Gaming Mode', 'Travel Tunes', 'Happy Hour', 'Sad Songs', 'Instrumental Bliss', 'Acoustic Afternoon', 'Cover Collection',
    'Mashup Mania', 'Remix Revolution', 'Old School', 'New Wave', 'Underground', 'Mainstream', 'Viral Tracks', 'Hidden Gems', 'All Time Faves', 'Fresh Finds'
];

// Album name generation arrays
$albumAdjectives = ['Silent', 'Golden', 'Lonely', 'Electric', 'Wild', 'Broken', 'Shining', 'Fading', 'Dancing', 'Burning', 'Frozen', 'Hidden', 'Falling', 'Rising', 'Midnight', 'Crystal', 'Neon', 'Velvet', 'Endless', 'Secret', 'Majestic', 'Radiant', 'Mystic', 'Vivid', 'Eternal', 'Sacred', 'Lost', 'Sacred', 'Infinite', 'Cosmic', 'Celestial'];
$albumNouns = ['Journey', 'Night', 'Heart', 'Sky', 'Light', 'Shadow', 'Fire', 'River', 'Star', 'Road', 'Rain', 'Sun', 'Moon', 'Ocean', 'Wind', 'Song', 'Memory', 'Echo', 'Wave', 'Story', 'Dream', 'Vision', 'Odyssey', 'Pulse', 'Spectrum', 'Reflection', 'Mirage', 'Oasis', 'Forest', 'Valley'];

// 4. ALBUMS
$albums = [];
$albumInserts = [];
$artistAlbumCount = array_fill(1, 100, 0); // artist_id => album count
for ($i=1; $i<=200; $i++) {
    $artist_id = rand(1,100);
    $name = $albumAdjectives[array_rand($albumAdjectives)] . ' ' . $albumNouns[array_rand($albumNouns)];
    $release_date = randomDate('2000-01-01', '2024-03-20');
    $genre = randomFromArray($genres);
    $music_number = rand(8,20);
    $image = "https://picsum.photos/seed/album$i/200/200";
    $albums[] = [
        'album_id' => $i,
        'artist_id' => $artist_id,
        'name' => $name,
        'release_date' => $release_date,
        'genre' => $genre,
        'music_number' => $music_number,
        'image' => $image
    ];
    $albumInserts[] = "INSERT INTO ALBUMS (album_id, artist_id, name, release_date, genre, music_number, image) VALUES ($i, $artist_id, '$name', '$release_date', '$genre', $music_number, '$image');";
    $artistAlbumCount[$artist_id]++;
}

// Song name generation arrays
$adjectives = ['Silent', 'Golden', 'Lonely', 'Electric', 'Wild', 'Broken', 'Shining', 'Fading', 'Dancing', 'Burning', 'Frozen', 'Hidden', 'Falling', 'Rising', 'Midnight', 'Crystal', 'Neon', 'Velvet', 'Endless', 'Secret'];
$nouns = ['Dream', 'Night', 'Heart', 'Sky', 'Light', 'Shadow', 'Fire', 'River', 'Star', 'Road', 'Rain', 'Sun', 'Moon', 'Ocean', 'Wind', 'Song', 'Memory', 'Echo', 'Wave', 'Story'];

// 5. SONGS
$songs = [];
srand(42); // for reproducibility
$songInserts = [];
for ($i=1; $i<=1000; $i++) {
    $album_id = rand(1,200);
    $title = $adjectives[array_rand($adjectives)] . ' ' . $nouns[array_rand($nouns)];
    $duration = rand(120, 360);
    $genre = randomFromArray($genres);
    $release_date = randomDate('2000-01-01', '2024-03-20');
    $rank = rand(1,100);
    $image = "https://picsum.photos/seed/song$i/200/200";
    $songs[] = [
        'song_id' => $i,
        'album_id' => $album_id,
        'title' => $title,
        'duration' => $duration,
        'genre' => $genre,
        'release_date' => $release_date,
        'rank' => $rank,
        'image' => $image
    ];
    $songInserts[] = "INSERT INTO SONGS (song_id, album_id, title, duration, genre, release_date, rank, image) VALUES ($i, $album_id, '$title', $duration, '$genre', '$release_date', $rank, '$image');";
}

// 6. PLAYLISTS
$playlists = [];
$playlistInserts = [];
for ($i=1; $i<=500; $i++) {
    $user_id = rand(1,100);
    $title = $playlistNames[array_rand($playlistNames)];
    $description = "Description for playlist $i";
    $date_created = randomDate('2020-01-01', '2024-03-20');
    $image = "https://picsum.photos/seed/playlist$i/200/200";
    $playlists[] = [
        'playlist_id' => $i,
        'user_id' => $user_id,
        'title' => $title,
        'description' => $description,
        'date_created' => $date_created,
        'image' => $image
    ];
    $playlistInserts[] = "INSERT INTO PLAYLISTS (playlist_id, user_id, title, description, date_created, image) VALUES ($i, $user_id, '$title', '$description', '$date_created', '$image');";
}

// 7. PLAYLIST_SONGS
$playlistSongs = [];
$playlistSongInserts = [];
$usedSongs = range(1, 1000);
shuffle($usedSongs);
for ($i = 1; $i <= 500; $i++) {
    // Ensure each playlist gets at least one unique song
    $playlist_id = $i;
    $song_id = array_pop($usedSongs);
    $date_added = randomDate('2020-01-01', '2024-03-20');
    $playlistSongs[] = [
        'playlistsong_id' => $i,
        'playlist_id' => $playlist_id,
        'song_id' => $song_id,
        'date_added' => $date_added
    ];
    $playlistSongInserts[] = "INSERT INTO PLAYLIST_SONGS (playlistsong_id, playlist_id, song_id, date_added) VALUES ($i, $playlist_id, $song_id, '$date_added');";
}
// Add additional random playlist-song pairs
for ($i = 501; $i <= 1000; $i++) {
    $playlist_id = rand(1, 500);
    $song_id = rand(1, 1000);
    $date_added = randomDate('2020-01-01', '2024-03-20');
    $playlistSongs[] = [
        'playlistsong_id' => $i,
        'playlist_id' => $playlist_id,
        'song_id' => $song_id,
        'date_added' => $date_added
    ];
    $playlistSongInserts[] = "INSERT INTO PLAYLIST_SONGS (playlistsong_id, playlist_id, song_id, date_added) VALUES ($i, $playlist_id, $song_id, '$date_added');";
}

// 8. PLAY_HISTORY
$playHistory = [];
$playHistoryInserts = [];
$play_id = 1;
for ($user_id = 1; $user_id <= 100; $user_id++) {
    $userSongs = range(1, 1000);
    shuffle($userSongs);
    for ($j = 0; $j < 10; $j++) {
        $song_id = $userSongs[$j];
        $playtime = randomDateTime('2020-01-01', '2024-03-20');
        $playHistory[] = [
            'play_id' => $play_id,
            'user_id' => $user_id,
            'song_id' => $song_id,
            'playtime' => $playtime
        ];
        $playHistoryInserts[] = "INSERT INTO PLAY_HISTORY (play_id, user_id, song_id, playtime) VALUES ($play_id, $user_id, $song_id, '$playtime');";
        $play_id++;
    }
}
// Add additional random play history entries
for ($i = $play_id; $i < $play_id + 200; $i++) {
    $user_id = rand(1, 100);
    $song_id = rand(1, 1000);
    $playtime = randomDateTime('2020-01-01', '2024-03-20');
    $playHistory[] = [
        'play_id' => $i,
        'user_id' => $user_id,
        'song_id' => $song_id,
        'playtime' => $playtime
    ];
    $playHistoryInserts[] = "INSERT INTO PLAY_HISTORY (play_id, user_id, song_id, playtime) VALUES ($i, $user_id, $song_id, '$playtime');";
}

// 3. ARTISTS (update total_albums to match actual count)
$artists = [];
$artistInserts = [];
for ($i=1; $i<=100; $i++) {
    $first = randomFromArray($firstNames);
    $last = randomFromArray($lastNames);
    $name = "$first $last";
    $genre = randomFromArray($genres);
    $date_joined = randomDate('2000-01-01', '2023-12-31');
    $total_num_music = rand(10, 100);
    $total_albums = $artistAlbumCount[$i];
    $listeners = rand(100, 1000000);
    $bio = "Bio for $name";
    $country_id = rand(1, count($countries));
    $gender = rand(0, 1) ? 'men' : 'women';
    $artist_image = "https://randomuser.me/api/portraits/$gender/" . $i . ".jpg";
    $artists[] = [
        'artist_id' => $i,
        'name' => $name,
        'genre' => $genre,
        'date_joined' => $date_joined,
        'total_num_music' => $total_num_music,
        'total_albums' => $total_albums,
        'listeners' => $listeners,
        'bio' => $bio,
        'country_id' => $country_id,
        'image' => $artist_image
    ];
}

// Create a map of country_id to artists for easier lookup
$country_artists = [];
foreach ($artists as $artist) {
    $country_id = $artist['country_id'];
    if (!isset($country_artists[$country_id])) {
        $country_artists[$country_id] = [];
    }
    $country_artists[$country_id][] = $artist['artist_id'];
}

// Ensure every user has at least one artist from the same country
foreach ($users as $user) {
    $user_country = $user['country_id'];
    
    // If no artists exist for this country, assign one
    if (!isset($country_artists[$user_country]) || empty($country_artists[$user_country])) {
        // Find an artist to modify
        $artist_to_modify = null;
        foreach ($artists as $key => $artist) {
            // Don't modify artists that are already assigned to other users' countries
            if (!isset($country_artists[$artist['country_id']]) || count($country_artists[$artist['country_id']]) > 1) {
                $artist_to_modify = $key;
                break;
            }
        }
        
        if ($artist_to_modify !== null) {
            // Remove from old country's list
            $old_country = $artists[$artist_to_modify]['country_id'];
            if (isset($country_artists[$old_country])) {
                $country_artists[$old_country] = array_diff($country_artists[$old_country], [$artists[$artist_to_modify]['artist_id']]);
            }
            
            // Update artist's country
            $artists[$artist_to_modify]['country_id'] = $user_country;
            
            // Add to new country's list
            if (!isset($country_artists[$user_country])) {
                $country_artists[$user_country] = [];
            }
            $country_artists[$user_country][] = $artists[$artist_to_modify]['artist_id'];
        }
    }
}

// Now generate artistInserts
for ($i=0; $i<count($artists); $i++) {
    $a = $artists[$i];
    $artistInserts[] = "INSERT INTO ARTISTS (artist_id, name, genre, date_joined, total_num_music, total_albums, listeners, bio, country_id, image) VALUES (" .
        $a['artist_id'] . ", '" . $a['name'] . "', '" . $a['genre'] . "', '" . $a['date_joined'] . "', " . $a['total_num_music'] . ", " . $a['total_albums'] . ", " . $a['listeners'] . ", '" . $a['bio'] . "', " . $a['country_id'] . ", '" . $a['image'] . "');";
}

// Write all SQL to a single file
$sqlFile = fopen("database_setup.sql", 'w');
// Clean tables before inserting
fwrite($sqlFile, "SET FOREIGN_KEY_CHECKS=0;\n");
fwrite($sqlFile, "TRUNCATE TABLE PLAYLIST_SONGS;\n");
fwrite($sqlFile, "TRUNCATE TABLE PLAY_HISTORY;\n");
fwrite($sqlFile, "TRUNCATE TABLE PLAYLISTS;\n");
fwrite($sqlFile, "TRUNCATE TABLE SONGS;\n");
fwrite($sqlFile, "TRUNCATE TABLE ALBUMS;\n");
fwrite($sqlFile, "TRUNCATE TABLE ARTISTS;\n");
fwrite($sqlFile, "TRUNCATE TABLE USERS;\n");
fwrite($sqlFile, "TRUNCATE TABLE COUNTRY;\n");
fwrite($sqlFile, "SET FOREIGN_KEY_CHECKS=1;\n");
foreach ($countryInserts as $sql) fwrite($sqlFile, $sql."\n");
foreach ($userInserts as $sql) fwrite($sqlFile, $sql."\n");
foreach ($artistInserts as $sql) fwrite($sqlFile, $sql."\n");
foreach ($albumInserts as $sql) fwrite($sqlFile, $sql."\n");
foreach ($songInserts as $sql) fwrite($sqlFile, $sql."\n");
foreach ($playlistInserts as $sql) fwrite($sqlFile, $sql."\n");
foreach ($playlistSongInserts as $sql) fwrite($sqlFile, $sql."\n");
foreach ($playHistoryInserts as $sql) fwrite($sqlFile, $sql."\n");
fclose($sqlFile);
echo "SQL file has been generated successfully";
?> 