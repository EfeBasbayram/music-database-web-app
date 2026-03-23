<?php
session_start();
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'efe_basbayram';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'] ?? '';
    $inputPassword = $_POST['password'] ?? '';
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT * FROM USERS WHERE username = :username");
        $stmt->execute(['username' => $inputUsername]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($inputPassword, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header('Location: homepage.php'); // Redirect to the PHP homepage
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Music Player - Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #10182a; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; color: #fff; }
        .container { background: #16213e; padding: 2rem 3rem; border-radius: 12px; box-shadow: 0 2px 16px rgba(16,24,42,0.18); text-align: center; }
        input[type="text"], input[type="password"] { width: 100%; padding: 0.75rem; margin: 0.5rem 0; border: 1.5px solid #23395d; border-radius: 6px; font-size: 1rem; background: #10182a; color: #fff; }
        input[type="text"]:focus, input[type="password"]:focus { border: 1.5px solid #fff; box-shadow: 0 0 0 2px #fff3; }
        button { background: #fff; color: #10182a; border: none; padding: 1rem 2rem; border-radius: 6px; font-size: 1.1rem; cursor: pointer; transition: background 0.2s, color 0.2s; width: 100%; }
        button:hover { background: #e0e6f7; color: #10182a; }
        .error { color: #e74c3c; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login to Music Player</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html> 