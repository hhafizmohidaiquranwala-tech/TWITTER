<?php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$my_id = $_SESSION['user_id'];
$profile_id = isset($_GET['id']) ? intval($_GET['id']) : $my_id;

// Fetch Profile User
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$user_res = $stmt->get_result();

if($user_res->num_rows == 0) {
    echo "User not found";
    exit();
}

$user = $user_res->fetch_assoc();
$is_me = ($my_id == $profile_id);

// Get Follow Stats
$followers = $conn->query("SELECT count(*) as c FROM follows WHERE following_id = $profile_id")->fetch_assoc()['c'];
$following = $conn->query("SELECT count(*) as c FROM follows WHERE follower_id = $profile_id")->fetch_assoc()['c'];

// Check if I follow them
$is_following = false;
if (!$is_me) {
    $check = $conn->prepare("SELECT * FROM follows WHERE follower_id = ? AND following_id = ?");
    $check->bind_param("ii", $my_id, $profile_id);
    $check->execute();
    if($check->get_result()->num_rows > 0) $is_following = true;
}

// Fetch Tweets
$t_sql = "SELECT tweets.*, 
            (SELECT count(*) FROM likes WHERE tweet_id = tweets.id) as like_count,
             (SELECT count(*) FROM comments WHERE tweet_id = tweets.id) as comment_count
          FROM tweets WHERE user_id = $profile_id ORDER BY created_at DESC";
$tweets = $conn->query($t_sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeonTweet - <?php echo htmlspecialchars($user['username']); ?></title>
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
            min-height: 100vh;
        }

       /* Sidebar (Reusing style implies copy paste if separate files needed) */
        .sidebar {
            width: 250px;
            padding: 20px;
            background: rgba(0,0,0,0.8);
            border-right: 1px solid var(--glass-border);
            position: fixed;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar a {
            color: #ccc;
            text-decoration: none;
            padding: 15px;
            display: block;
            margin-bottom: 5px;
        }

        .main-content {
            margin-left: 290px;
            width: 600px;
            padding: 20px;
        }

        .profile-header {
            background: var(--glass-bg);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid var(--neon-blue);
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.1);
            margin-bottom: 20px;
        }

        .avatar-large {
            width: 100px;
            height: 100px;
            background: #333;
            border-radius: 50%;
            margin: 0 auto 15px;
            border: 3px solid var(--neon-pink);
            box-shadow: 0 0 15px var(--neon-pink);
        }

        h2 {
            margin: 10px 0;
            font-family: 'Orbitron', sans-serif;
            text-shadow: 0 0 10px var(--neon-blue);
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0;
            color: #ccc;
        }

        .stats span {
            font-weight: bold;
            color: var(--neon-pink);
            font-size: 1.2em;
        }

        .btn-action {
            display: inline-block;
            padding: 10px 20px;
            background: transparent;
            border: 2px solid var(--neon-blue);
            color: var(--neon-blue);
            text-decoration: none;
            border-radius: 20px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }
        
        .btn-action:hover {
            background: var(--neon-blue);
            color: #000;
            box-shadow: 0 0 15px var(--neon-blue);
        }

        .tweet-list {
            margin-top: 20px;
        }

        .tweet-item {
            background: rgba(255,255,255,0.02);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            border-bottom: 1px solid var(--glass-border);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2 style="color:var(--neon-blue); margin-top:0;">NEON TWEET</h2>
        <a href="home.php">Home</a>
        <a href="profile.php?id=<?php echo $my_id; ?>">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="profile-header">
            <div class="avatar-large"></div> <!-- Placeholder for image -->
            <h2>@<?php echo htmlspecialchars($user['username']); ?></h2>
            <p><?php echo htmlspecialchars($user['bio'] ?: "No bio yet."); ?></p>
            
            <div class="stats">
                <div><span><?php echo $following; ?></span> Following</div>
                <div><span><?php echo $followers; ?></span> Followers</div>
            </div>

            <?php if ($is_me): ?>
                <a href="edit_profile.php" class="btn-action">Edit Profile</a>
            <?php else: ?>
                <form action="action.php" method="POST">
                    <input type="hidden" name="follow_user_id" value="<?php echo $profile_id; ?>">
                    <button type="submit" class="btn-action">
                        <?php echo $is_following ? "Unfollow" : "Follow"; ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <h3 style="color:var(--neon-pink); font-family:'Orbitron';">Tweets</h3>
        <div class="tweet-list">
            <?php while($row = $tweets->fetch_assoc()): ?>
                <div class="tweet-item">
                    <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                    <small style="color:#666;"><?php echo date("M j, g:i a", strtotime($row['created_at'])); ?></small>
                    <div style="margin-top:5px; color:#888;">
                        Likes: <?php echo $row['like_count']; ?> | Comments: <?php echo $row['comment_count']; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
