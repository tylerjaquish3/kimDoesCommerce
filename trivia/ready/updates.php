<?php

include('../datalogin.php');

$currentSeason = 1;
$currentRound = 1;
$gameOver = false;

header("Cache-Control: no-cache");
header("Content-Type: text/event-stream");

$sql = "SELECT * FROM rounds";
$result = mysqli_query($conn, $sql);
echo "event: game\n";
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result))
    {
        if ($row['active'] == 1) {
            $currentRound = $row['id'];
            $gameOver = false;
        } else {
            $gameOver = true;
        }
    }
} else {
    $gameOver = true;
}

if ($gameOver) {
    echo 'data: {"gameOver": "true"}';
} else {
    echo 'data: {"gameOver": "false"}';
}
echo "\n\n";

if (!$gameOver) {

    // Get the questions and timer to update the users' page
    $sql = "SELECT * FROM questions WHERE round_id = $currentRound and active = 1 ORDER BY sort_order DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_array($result))
        {
            $questionId = $row['id'];

            $sql = "SELECT * FROM answers
                JOIN users on users.id = answers.user_id
                WHERE question_id = $questionId";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while($ans = mysqli_fetch_array($result))
                {
                    $maxPoints = $ans['max_points'];
                    if ($ans['correct'] == 2) {
                        $maxPoints = round($ans['max_points'] / 2, 0);
                    }
                    $ansArray[] = [
                        'name' => $ans['name'],
                        'answer' => $ans['answer'],
                        'correct' => $ans['correct'],
                        'points' => $maxPoints
                    ];
                }
            }
            echo "event: question\n";
            if ($row['reveal']) {
                echo 'data: {"id": "' . $row['id'] . '", "question": "'.$row['question'] . '", "answer": "'.$row['answer'].'", "sort_order": "'.$row['sort_order'].'", "allAnswers":{"players":'.json_encode($ansArray).'}}';
            } else {
                echo 'data: {"id": "' . $row['id'] . '", "question": "'.$row['question'].'"}';
            }
            echo "\n\n";

            $start  = strtotime($row['start_time']);
            $now = strtotime(date('Y-m-d H:i:s'));
            $timeLeft = 45 - ($now - $start);
            $pct = ($timeLeft / 45) * 100;

            echo "event: timer\n";
            echo 'data: {"id": "' . $row['id'] . '", "time": "'.$pct.'"}';
            echo "\n\n";
        }
    }

    // Get the answers to display for admin
    $sql = "SELECT answers.user_id, answers.answer, maxsubmit, question_id, name FROM answers
    JOIN questions ON questions.id = answers.question_id
    JOIN users ON users.id = answers.user_id
    INNER JOIN ( SELECT user_id, MAX(submitted_at) AS maxsubmit FROM answers GROUP BY user_id
    ) ms ON answers.user_id = ms.user_id AND submitted_at = maxsubmit
    WHERE questions.round_id = $currentRound";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_array($result))
        {
            $ansArray[] = [
                'user_id' => $row['user_id'],
                'name' => $row['name'],
                'answer' => $row['answer'],
                'question_id' => $row['question_id']
            ];
        }
        echo "event: answers\n";
        echo 'data: {"allAnswers":{"players":'.json_encode($ansArray).'}}';
        echo "\n\n";
    }

    // Get the standings
    $sql = "SELECT SUM(points) AS total, MAX(points.question_id) as maxQ, name FROM points
        JOIN users ON users.id = points.user_id
        LEFT JOIN questions ON questions.id = points.question_id
        LEFT JOIN rounds ON rounds.id = questions.round_id
        WHERE rounds.id = $currentRound OR question_id = 0
        GROUP BY name ORDER BY total DESC";
    $result = mysqli_query($conn, $sql);
    $rank = 1;
    $stArray = [];
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_array($result))
        {
            $stArray[] = [
                'rank' => $rank,
                'name' => $row['name'],
                'points' => $row['total'],
                'max_question' => $row['maxQ']
            ];
            $rank++;
        }
    }
    echo "event: standings\n";
    echo 'data: {"standings":{"players":'.json_encode($stArray).'}}';
    echo "\n\n";
}

ob_end_flush();
flush();

?>
