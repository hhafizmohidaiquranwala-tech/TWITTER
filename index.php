<?php
include 'db.php';
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='home.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeonTweet - Welcome</title>
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
            text-align: center;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.2);
            animation: float 6s ease-in-out infinite;
            max-width: 400px;
            width: 90%;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 3em;
            margin-bottom: 20px;
            background: linear-gradient(90deg, var(--neon-blue), var(--neon-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 10px rgba(0, 243, 255, 0.5);
        }

        p {
            margin-bottom: 40px;
            color: #ccc;
            font-size: 1.1em;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: none;
            border-radius: 50px;
            background: transparent;
            color: white;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            overflow: hidden;
            transition: 0.3s;
            border: 2px solid var(--neon-blue);
            box-shadow: 0 0 10px var(--neon-blue), inset 0 0 10px var(--neon-blue);
        }

        .btn:hover {
            background: var(--neon-blue);
            color: #000;
            box-shadow: 0 0 30px var(--neon-blue), inset 0 0 20px var(--neon-blue);
        }

        .btn.secondary {
            border-color: var(--neon-pink);
            box-shadow: 0 0 10px var(--neon-pink), inset 0 0 10px var(--neon-pink);
        }

        .btn.secondary:hover {
            background: var(--neon-pink);
            color: #000;
            box-shadow: 0 0 30px var(--neon-pink), inset 0 0 20px var(--neon-pink);
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>NEON TWEET</h1>
        <p>Join the future of conversation.</p>
        <button class="btn" onclick="window.location.href='login.php'">Login</button>
        <button class="btn secondary" onclick="window.location.href='signup.php'">Sign Up</button>
    </div>

</body>
</html>
