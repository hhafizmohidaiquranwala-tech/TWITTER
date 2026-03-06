<?php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='home.php';</script>";
    exit();
}

$tweet_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch Tweet to ensure ownership
$stmt = $conn->prepare("SELECT * FROM tweets WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $tweet_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Tweet not found or unauthorized.";
    exit();
}

$tweet = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = trim($_POST['content']);
    if (!empty($content)) {
        $update = $conn->prepare("UPDATE tweets SET content = ? WHERE id = ?");
        $update->bind_param("si", $content, $tweet_id);
        $update->execute();
        echo "<script>window.location.href='home.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tweet</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 400px;
            padding: 40px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.2);
        }

        textarea {
            width: 90%;
            padding: 10px;
            margin-bottom: 20px;
            background: rgba(0,0,0,0.5);
            border: 1px solid var(--neon-blue);
            color: white;
            border-radius: 5px;
            height: 100px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: var(--neon-blue);
            border: none;
            color: black;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 0 10px var(--neon-blue);
            transition: 0.3s;
        }

        button:hover {
            box-shadow: 0 0 20px var(--neon-pink);
            background: var(--neon-pink);
        }
    </style>
</head>
<body>

    <div class="container">
        <h2 style="text-align:center; color: var(--neon-blue); font-family:'Orbitron';">EDIT TWEET</h2>
        <form method="POST">
            <textarea name="content" required><?php echo htmlspecialchars($tweet['content']); ?></textarea>
            <button type="submit">Update Tweet</button>
            <button type="button" onclick="window.location.href='home.php'" style="background:transparent; border:1px solid #555; color:#ccc; margin-top:10px; box-shadow:none;">Cancel</button>
        </form>
    </div>

</body>
</html>
