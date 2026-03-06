<?php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Post a Tweet
    if (isset($_POST['tweet_content'])) {
        $content = trim($_POST['tweet_content']);
        if (!empty($content)) {
            $stmt = $conn->prepare("INSERT INTO tweets (user_id, content) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $content);
            $stmt->execute();
        }
        echo "<script>window.location.href='home.php';</script>";
        exit();
    }

    // Delete Tweet
    if (isset($_POST['delete_tweet_id'])) {
        $tweet_id = $_POST['delete_tweet_id'];
        $stmt = $conn->prepare("DELETE FROM tweets WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $tweet_id, $user_id);
        $stmt->execute();
        echo "<script>window.history.back();</script>";
        exit();
    }

    // Follow User
    if (isset($_POST['follow_user_id'])) {
        $follow_id = $_POST['follow_user_id'];
        // Check if already following
        $check = $conn->prepare("SELECT * FROM follows WHERE follower_id = ? AND following_id = ?");
        $check->bind_param("ii", $user_id, $follow_id);
        $check->execute();
        if ($check->fetch()) {
            // Unfollow
            $stmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
            $stmt->bind_param("ii", $user_id, $follow_id);
        } else {
            // Follow
            $stmt = $conn->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $follow_id);
        }
        $stmt->execute();
        echo "<script>window.history.back();</script>";
        exit();
    }

    // Like Tweet
    if (isset($_POST['like_tweet_id'])) {
        $tweet_id = $_POST['like_tweet_id'];
        $check = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND tweet_id = ?");
        $check->bind_param("ii", $user_id, $tweet_id);
        $check->execute();
        if ($check->fetch()) {
            $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND tweet_id = ?");
        } else {
            $stmt = $conn->prepare("INSERT INTO likes (user_id, tweet_id) VALUES (?, ?)");
        }
        $stmt->bind_param("ii", $user_id, $tweet_id);
        $stmt->execute();
        echo "<script>window.history.back();</script>";
        exit();
    }

    // Comment
    if (isset($_POST['comment_content']) && isset($_POST['tweet_id'])) {
        $content = trim($_POST['comment_content']);
        $tweet_id = $_POST['tweet_id'];
        if (!empty($content)) {
            $stmt = $conn->prepare("INSERT INTO comments (user_id, tweet_id, content) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $user_id, $tweet_id, $content);
            $stmt->execute();
        }
        echo "<script>window.history.back();</script>";
        exit();
    }
}
?>
