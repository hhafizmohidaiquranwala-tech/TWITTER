<?php
include 'db.php';

if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='home.php';</script>";
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Username or Email already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            echo "<script>alert('Registration Successful! Please Login.'); window.location.href='login.php';</script>";
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeonTweet - Sign Up</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@300;400;700&display=swap');

        :root {
            --bg-dark: #050505;
            --neon-blue: #00f3ff;
            --neon-pink: #ff00ff;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #ffffff;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Roboto', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            background: radial-gradient(circle at center, #1a1a2e 0%, #000000 100%);
        }

        .container {
            width: 100%;
            max-width: 400px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(255, 0, 255, 0.2);
            text-align: center;
        }

        h2 {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-pink);
            text-shadow: 0 0 10px var(--neon-pink);
            margin-bottom: 30px;
        }

        input {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid var(--neon-blue);
            color: white;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            box-shadow: 0 0 10px var(--neon-blue);
        }

        button {
            width: 95%;
            padding: 12px;
            margin-top: 20px;
            background: var(--neon-pink);
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            box-shadow: 0 0 15px var(--neon-pink);
            transition: 0.3s;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 25px var(--neon-pink);
        }

        .link {
            display: block;
            margin-top: 20px;
            color: #ccc;
            font-size: 0.9em;
        }

        .link a {
            color: var(--neon-blue);
            text-decoration: none;
        }

        .error {
            color: #ff4444;
            margin-bottom: 10px;
            text-shadow: 0 0 5px red;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>CREATE ACCOUNT</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <div class="link">
            Already have an account? <a href="#" onclick="window.location.href='login.php'; return false;">Login</a>
        </div>
    </div>

</body>
</html>
