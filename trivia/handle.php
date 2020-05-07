<?php

include('datalogin.php');

// var_dump($_POST);die;

// Add the user
if ($_POST && array_key_exists('action', $_POST) && $_POST['action'] == 'register') {

    $sql = "SELECT * FROM rounds WHERE active = 1";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_array($result))
        {
            $roundId = $row['id'];
        }
    }

    $name = $_POST['name-input'];
    $sql = "INSERT INTO users (name, round_id) VALUES ('{$name}', $roundId)";
    $result = mysqli_query($conn, $sql);

    $lastId = $conn->insert_id;

    $sql = "INSERT INTO points (user_id, question_id, points) VALUES ($lastId, 0, 0)";
    $result = mysqli_query($conn, $sql);

    header("Location: ".URL."/ready?id=".$lastId);
	die();
}

// User submitted an answer
if ($_POST && array_key_exists('action', $_POST) && $_POST['action'] == 'answer-submit') {

    $submittedAt = date('Y-m-d H:i:s');
    $userId = $_POST['user_id'];
    $answer = mysqli_real_escape_string($conn, $_POST['answer']);
    $questionId = $_POST['question_id'];

    $sql = "SELECT * FROM questions WHERE id = $questionId";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_array($result))
        {
            $start  = strtotime($row['start_time']);
            $submitted = strtotime($submittedAt);
            $timeLeft = $submitted - $start;
            $maxPoints = round(100 - (($timeLeft / 45) * 100), 0);
        }
    }

    $sql = "INSERT INTO answers (user_id, question_id, answer, max_points, submitted_at)
        VALUES ($userId, $questionId, '{$answer}', $maxPoints, '{$submittedAt}')";
    $result = mysqli_query($conn, $sql);

    echo json_encode(['max_points' => $maxPoints]);
}

if ($_POST && array_key_exists('action', $_POST) && $_POST['action'] == 'test') {

    $sql = "SELECT * FROM rounds";
    $result = mysqli_query($conn, $sql);
    echo "event: game\n";
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_array($result))
        {
            if ($row['active'] == 1) {
                $currentRound = $row['id'];
                echo 'data: {"game": "true"}';
            } else {
                echo 'data: {"game": "false"}';
            }
        }
    }
    echo "\n\n";
}




?>