<?php

include('../datalogin.php');


// Reset game
if ($_POST && array_key_exists('action', $_POST) && $_POST['action'] == 'reset') {

    $sql = "UPDATE questions SET start_time=null, active=0, reveal=0";
    $result = mysqli_query($conn, $sql);
    $sql = "DELETE FROM answers";
    $result = mysqli_query($conn, $sql);
    $sql = "DELETE FROM points";
    $result = mysqli_query($conn, $sql);

    echo json_encode('Game has been reset!');
}

// Launch a question
if ($_POST && array_key_exists('action', $_POST) && $_POST['action'] == 'launch') {

    $id = $_POST['question_id'];
    $now = date('Y-m-d H:i:s');
    $sql = "UPDATE questions SET start_time='{$now}', active=1 WHERE id = {$id}";
    $result = mysqli_query($conn, $sql);

    echo json_encode('Question has been launched!');
}

// Reveal an answer
if ($_POST && array_key_exists('action', $_POST) && $_POST['action'] == 'reveal') {

    $id = $_POST['question_id'];
    $now = date('Y-m-d H:i:s');
    $sql = "UPDATE questions SET reveal=1 WHERE id = {$id}";
    $result = mysqli_query($conn, $sql);

    echo json_encode('Answer has been revealed!');
}

// Answer was right
if ($_POST && array_key_exists('action', $_POST) && $_POST['action'] == 'correct') {

    $questionId = $_POST['question_id'];
    $userId = $_POST['user_id'];

    // Look up points
    $sql = "SELECT answers.user_id, answer, maxsubmit, question_id, max_points FROM answers
        INNER JOIN ( SELECT user_id, MAX(submitted_at) AS maxsubmit FROM answers GROUP BY user_id
        ) ms ON answers.user_id = ms.user_id AND submitted_at = maxsubmit
        WHERE answers.user_id = $userId AND question_id = $questionId";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_array($result))
        {
            $maxPoints = $row['max_points'];
        }
    }

    $sql = "INSERT INTO points (user_id, question_id, points) VALUES ($userId, $questionId, $maxPoints)
        ON DUPLICATE KEY UPDATE points = $maxPoints";
    $result = mysqli_query($conn, $sql);

    $sql = "UPDATE answers SET correct = 1 where question_id = $questionId and user_id = $userId";
    $result = mysqli_query($conn, $sql);

    echo json_encode('Points have been awarded!');
}

// Answer was wrong
if ($_POST && array_key_exists('action', $_POST) && $_POST['action'] == 'wrong') {

    $questionId = $_POST['question_id'];
    $userId = $_POST['user_id'];

    $sql = "INSERT INTO points (user_id, question_id, points) VALUES ($userId, $questionId, 0)
        ON DUPLICATE KEY UPDATE points = 0";
    $result = mysqli_query($conn, $sql);

    echo json_encode('Points have been awarded!');
}

// Reset game
if ($_POST && array_key_exists('action', $_POST) && $_POST['action'] == 'game-over') {

    $sql = "UPDATE rounds SET active=0";
    $result = mysqli_query($conn, $sql);

    echo json_encode('Game over!');
}

?>