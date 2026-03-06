<?php
include 'db.php';

if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='home.php';</script>";
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            echo "<script>window.location.href='home.php';</script>";
            exit();
        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeonTweet - Login</title>
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
            background: radial-gradient(circle at center, #000000 0%, #1a1a2e 100%);
        }

        .container {
            width: 100%;
            max-width: 400px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.2);
            text-align: center;
        }

        h2 {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-blue);
            text-shadow: 0 0 10px var(--neon-blue);
            margin-bottom: 30px;
        }

        input {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid var(--neon-pink);
            color: white;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            box-shadow: 0 0 10px var(--neon-pink);
        }

        button {
            width: 95%;
            padding: 12px;
            margin-top: 20px;
            background: var(--neon-blue);
            border: none;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            box-shadow: 0 0 15px var(--neon-blue);
            transition: 0.3s;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 25px var(--neon-blue);
        }

        .link {
            display: block;
            margin-top: 20px;
            color: #ccc;
            font-size: 0.9em;
        }

        .link a {
            color: var(--neon-pink);
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
        <h2>LOGIN</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="link">
            Don't have an account? <a href="#" onclick="window.location.href='signup.php'; return false;">Sign Up</a>
        </div>
    </div>

</body>
</html>
