<?php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bio = trim($_POST['bio']);
    $new_username = trim($_POST['username']);

    // Check if new username exists separately
    if ($new_username != $_SESSION['username']) {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $new_username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $message = "Username already taken!";
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, bio = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_username, $bio, $user_id);
            if ($stmt->execute()) {
                $_SESSION['username'] = $new_username;
                echo "<script>alert('Profile Updated!'); window.location.href='profile.php';</script>";
                exit();
            }
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE id = ?");
        $stmt->bind_param("si", $bio, $user_id);
        if ($stmt->execute()) {
             echo "<script>alert('Profile Updated!'); window.location.href='profile.php';</script>";
             exit();
        }
    }
}

// Fetch current details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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

        input, textarea {
            width: 90%;
            padding: 10px;
            margin-bottom: 20px;
            background: rgba(0,0,0,0.5);
            border: 1px solid var(--neon-blue);
            color: white;
            border-radius: 5px;
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
        <h2 style="text-align:center; color: var(--neon-blue); font-family:'Orbitron';">EDIT PROFILE</h2>
        <?php if($message) echo "<p style='color:red; text-align:center;'>$message</p>"; ?>
        
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            
            <label>Bio</label>
            <textarea name="bio" rows="4"><?php echo htmlspecialchars($user['bio']); ?></textarea>
            
            <button type="submit">Save Changes</button>
            <button type="button" onclick="window.history.back()" style="background:transparent; border:1px solid #555; color:#ccc; margin-top:10px; box-shadow:none;">Cancel</button>
        </form>
    </div>

</body>
</html>
