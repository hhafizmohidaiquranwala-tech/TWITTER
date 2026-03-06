<?php
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch Tweets
$sql = "SELECT tweets.*, users.username, users.avatar, 
        (SELECT COUNT(*) FROM likes WHERE likes.tweet_id = tweets.id) AS like_count,
        (SELECT COUNT(*) FROM comments WHERE comments.tweet_id = tweets.id) AS comment_count,
        (SELECT count(*) FROM likes WHERE likes.tweet_id = tweets.id AND likes.user_id = $user_id) AS user_liked
        FROM tweets 
        JOIN users ON tweets.user_id = users.id 
        ORDER BY tweets.created_at DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeonTweet - Home</title>
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

        /* Sidebar */
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

        .logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5em;
            color: var(--neon-blue);
            margin-bottom: 30px;
            text-shadow: 0 0 10px var(--neon-blue);
        }

        .nav-link {
            display: block;
            padding: 15px;
            color: #ccc;
            text-decoration: none;
            font-size: 1.1em;
            transition: 0.3s;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--glass-bg);
            color: var(--neon-pink);
            text-shadow: 0 0 5px var(--neon-pink);
            box-shadow: 0 0 10px rgba(255, 0, 255, 0.2);
        }

        /* Feed */
        .feed {
            margin-left: 290px; /* Sidebar width + padding */
            width: 600px;
            padding: 20px;
        }

        .tweet-box {
            background: var(--glass-bg);
            padding: 20px;
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            margin-bottom: 20px;
            box-shadow: 0 0 15px rgba(0, 243, 255, 0.1);
        }

        textarea {
            width: 100%;
            background: rgba(0,0,0,0.5);
            border: 1px solid var(--neon-blue);
            color: white;
            padding: 10px;
            border-radius: 10px;
            resize: none;
            height: 80px;
            outline: none;
            font-family: 'Roboto', sans-serif;
            margin-bottom: 10px;
        }

        textarea:focus {
            box-shadow: 0 0 10px var(--neon-blue);
        }

        .btn-tweet {
            background: var(--neon-blue);
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            float: right;
            transition: 0.3s;
            box-shadow: 0 0 10px var(--neon-blue);
        }

        .btn-tweet:hover {
            box-shadow: 0 0 20px var(--neon-blue);
        }

        /* Tweets */
        .tweet {
            background: rgba(255, 255, 255, 0.02);
            padding: 20px;
            border-bottom: 1px solid var(--glass-border);
            border-radius: 10px;
            margin-bottom: 15px;
            position: relative;
            transition: 0.3s;
        }
        
        .tweet:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: #333;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid var(--neon-pink);
            box-shadow: 0 0 5px var(--neon-pink);
        }

        .username {
            font-weight: bold;
            color: var(--neon-pink);
            text-decoration: none;
        }
        
        .username:hover {
            text-decoration: underline;
            text-shadow: 0 0 5px var(--neon-pink);
        }

        .content {
            font-size: 1.1em;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .actions {
            display: flex;
            gap: 20px;
            color: #888;
        }

        .action-btn {
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            transition: 0.3s;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .action-btn:hover {
            color: var(--neon-blue);
            text-shadow: 0 0 5px var(--neon-blue);
        }

        .liked {
            color: var(--neon-pink) !important;
            text-shadow: 0 0 5px var(--neon-pink);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
                padding: 10px;
                align-items: center;
            }
            .sidebar .nav-link span { display: none; }
            .feed { margin-left: 80px; width: calc(100% - 100px); }
            .logo { font-size: 0.8em; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">NEON TWEET</div>
        <a href="home.php" class="nav-link active">Home</a>
        <a href="profile.php?id=<?php echo $user_id; ?>" class="nav-link">Profile</a>
        <a href="logout.php" class="nav-link">Logout</a>
    </div>

    <div class="feed">
        <!-- Tweet Box -->
        <div class="tweet-box">
            <form action="action.php" method="POST">
                <textarea name="tweet_content" placeholder="What's happening?" required></textarea>
                <button type="submit" class="btn-tweet">Tweet</button>
                <div style="clear: both;"></div>
            </form>
        </div>

        <!-- Tweets Stream -->
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="tweet">
                <div class="user-info">
                    <div class="avatar"></div>
                    <div>
                        <a href="profile.php?id=<?php echo $row['user_id']; ?>" class="username">@<?php echo htmlspecialchars($row['username']); ?></a>
                        <span style="color: #666; font-size: 0.8em; margin-left: 10px;"><?php echo date("M j, g:i a", strtotime($row['created_at'])); ?></span>
                    </div>
                    <?php if($row['user_id'] == $user_id): ?>
                    <div style="margin-left: auto; display:flex; gap:10px;">
                        <button onclick="window.location.href='edit_tweet.php?id=<?php echo $row['id']; ?>'" style="background: none; border: none; color: var(--neon-blue); cursor: pointer; font-size: 0.9em;">Edit</button>
                        <form action="action.php" method="POST" style="display:inline;">
                            <input type="hidden" name="delete_tweet_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" style="background: none; border: none; color: #ff4444; cursor: pointer; font-size: 0.9em;">Delete</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="content">
                    <?php echo nl2br(htmlspecialchars($row['content'])); ?>
                </div>
                
                <div class="actions">
                    <!-- Like Form -->
                    <form action="action.php" method="POST" style="display:inline;">
                        <input type="hidden" name="like_tweet_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="action-btn <?php echo $row['user_liked'] > 0 ? 'liked' : ''; ?>">
                            Like (<?php echo $row['like_count']; ?>)
                        </button>
                    </form>

                    <!-- Comment Indicator (could link to single tweet page if implemented, or just show count) -->
                    <button class="action-btn" onclick="document.getElementById('comment-box-<?php echo $row['id']; ?>').style.display='block'">
                        Comment (<?php echo $row['comment_count']; ?>)
                    </button>
                </div>

                <!-- Comment Box (Hidden by default) -->
                <div id="comment-box-<?php echo $row['id']; ?>" style="display:none; margin-top: 10px;">
                    <form action="action.php" method="POST">
                        <input type="hidden" name="tweet_id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="comment_content" placeholder="Write a comment..." style="width: 70%; background: #222; border: 1px solid #444; color: white; padding: 5px; border-radius: 5px;">
                        <button type="submit" style="padding: 5px 10px; background: var(--neon-blue); border: none; border-radius: 5px; cursor: pointer;">Post</button>
                    </form>
                </div>
                
                <!-- Display Comments (Simple list) -->
                <?php
                    $tid = $row['id'];
                    $c_sql = "SELECT comments.content, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE tweet_id = $tid ORDER BY comments.created_at ASC LIMIT 3";
                    $c_res = $conn->query($c_sql);
                    if ($c_res->num_rows > 0) {
                        echo "<div style='margin-top: 10px; padding-top: 10px; border-top: 1px solid #333; font-size: 0.9em; color: #aaa;'>";
                        while($c = $c_res->fetch_assoc()) {
                            echo "<div><strong style='color: var(--neon-blue)'>" . htmlspecialchars($c['username']) . ":</strong> " . htmlspecialchars($c['content']) . "</div>";
                        }
                        echo "</div>";
                    }
                ?>
            </div>
        <?php endwhile; ?>
    </div>

</body>
</html>
