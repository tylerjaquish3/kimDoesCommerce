<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <meta property="og:title" content="Tyler's Trivia Time" />
    <meta property="og:description" content="Time for Trivia with Tyler!" />
    <meta property="og:url" content="http://kimdoescommerce.com/trivia" />
    <meta property="og:image" content="http://kimdoescommerce.com/images/logo.png" />

    <title>Tyler's Trivia Time</title>

	<!-- Fontawesome Icon font -->
    <link rel="stylesheet" href="/trivia/css/font-awesome.min.css">
	<!-- bootstrap.min css -->
    <link rel="stylesheet" href="/trivia/css/bootstrap.min.css">
	<!-- Animate.css -->
    <!-- <link rel="stylesheet" href="js/animate.css"> -->
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">

	<!-- Main Stylesheet -->
    <link rel="stylesheet" href="/trivia/css/style.css">
    <link rel="stylesheet" href="/trivia/css/responsive.css">

</head>
<body>
    <div class="container">
        <div class="row text-center">
            <div class="col-xs-12">
                <h1>Control Center</h1>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-xs-12">
                <button class="btn btn-primary" id="reset-game">Reset</button>
                <button class="btn btn-success" id="game-over">Game Over</button>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-xs-12">
                <table id="datatable-questions">
                    <thead>
                        <th>#</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Launch</th>
                        <th>Reveal</th>
                    </thead>
                    <tbody>
                        <?php
                        include('../datalogin.php');
                        $sql = "SELECT questions.id, questions.sort_order, questions.question, questions.answer FROM questions
                            JOIN rounds ON rounds.id = questions.round_id
                            WHERE rounds.active = 1";
                        $results = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($results) > 0) {
                            while($row = mysqli_fetch_array($results))
                            { ?>
                                <tr>
                                    <td><?php echo $row['sort_order']; ?></td>
                                    <td><?php echo $row['question']; ?></td>
                                    <td><?php echo $row['answer']; ?></td>
                                    <td><a class="btn btn-success" data-l-id="<?php echo $row['id'];?>" onclick="launch(<?php echo $row['id'];?>);">Launch</a></td>
                                    <td><a class="btn btn-warning" data-r-id="<?php echo $row['id'];?>" onclick="reveal(<?php echo $row['id'];?>);">Reveal</a></td>
                                </tr>
                            <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row text-center">
            <div class="col-xs-10">
                <table id="answers">
                    <thead>
                        <th>User</th>
                        <th>Answer</th>
                        <th>Resolve</th>
                        <th>Done</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-xs-2">
                Countdown Timer (45s)
                <div id="timer">
                    <div class="progress vertical">
                        <div class="progress-bar" id="bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:100%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row text-center" id="standings">
            <div class="col-xs-12">
                <table id="datatable-standings">
                    <thead>
                        <th>Rank</th>
                        <th>User</th>
                        <th>Points</th>
                    </thead>
                    <tbody>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

<!-- Main jQuery -->
<script type="text/javascript" src="/trivia/js/jquery.min.js"></script>
<!-- Bootstrap 3.1 -->
<script type="text/javascript" src="/trivia/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script type="text/javascript" src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">

    questionsTable = $('#datatable-questions').DataTable({
        "order": [[ 0, "asc" ]],
        "dom": 'rt<"bottom"ip><"clear">'
    });

    standingsTable = $('#datatable-standings').DataTable({
        "order": [[ 0, "asc" ]],
        "dom": 'rt<"bottom"ip><"clear">'
    });

    const evtSource = new EventSource("../ready/updates.php");

    allAnswers = [];
    savedAnswers = [];
    savedStandings = [];

    evtSource.addEventListener("timer", function(event) {
        var id = JSON.parse(event.data).id;
        var time = JSON.parse(event.data).time;

        if (time > 0) {
            $('#bar').css('width', time+'%');
        } else {
            $('#bar').css('width', '0%');
        }
    });

    evtSource.addEventListener("answers", function(event) {
        var allAnswers = JSON.parse(event.data).allAnswers;
        allAnswers = allAnswers.players;

        // console.log(allAnswers);
        // console.log(savedAnswers);
        allAnswers.forEach(function (item) {
            savedAnswers = add(savedAnswers, item);
        });
    });

    function add(arr, item) {
        const { length } = arr;
        const id = length + 1;
        const found = arr.some(el => el.user_id === item.user_id);
        let currentQuestionId = localStorage.getItem('currentQuestionId');

        // console.log(item.question_id);
        // console.log(currentQuestionId);
        if (item.question_id == currentQuestionId) {
            if (!found) {
                $("#answers").find('tbody')
                .append($('<tr>')
                    .append($('<td>').append(item.name))
                    .append($('<td>').append(item.answer))
                    .append($('<td>').append('<a class="btn btn-success correct" id="'+item.user_id+'">Correct</a><a class="btn btn-warning half" id="'+item.user_id+'">Half-Right</a><a class="btn btn-danger wrong" id="'+item.user_id+'">Wrong</a>'))
                );

                arr.push({ id, user_id: item.user_id, answer: item.answer });
            }
        }

        return arr;
    }

    evtSource.addEventListener("standings", function(event) {
        var standings = JSON.parse(event.data).standings;
        standings = standings.players;

        $('#standings tbody').empty();

        standings.forEach(function (item) {
            $("#standings").find('tbody')
            .append($('<tr>')
                .append($('<td>').append(item.rank))
                .append($('<td>').append(item.name))
                .append($('<td>').append(item.points))
            );
        });
    });

    function launch(id) {
        $.ajax({
            url: 'control.php',
            type: "POST",
            data: {
                action: 'launch',
                question_id: id
            },
            async: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
            }
        });

        localStorage.setItem('currentQuestionId', id);
        $('#answers tbody').empty();
        $('#standings tbody').empty();
        savedAnswers = [];
        savedStandings = [];

        $('[data-l-id="'+id+'"]').removeClass('btn-success').addClass('btn-danger');
    }

    function reveal(id) {
        $.ajax({
            url: 'control.php',
            type: "POST",
            data: {
                action: 'reveal',
                question_id: id
            },
            async: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
            }
        });

        $('[data-r-id="'+id+'"]').removeClass('btn-warning').addClass('btn-danger');
    }

    $('#reset-game').click(function () {
        $.ajax({
            url: 'control.php',
            type: "POST",
            data: {
                action: 'reset'
            },
            async: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
            }
        });

        $('#bar').css('width', '100%');
        localStorage.setItem('currentQuestionId', 0);
        $('#answers tbody').empty();
    });

    $('#game-over').click(function () {
        $.ajax({
            url: 'control.php',
            type: "POST",
            data: {
                action: 'game-over'
            },
            async: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
            }
        });

        $('#bar').css('width', '100%');
        localStorage.setItem('currentQuestionId', 0);
        $('#answers tbody').empty();
    });

    $(document).on('click', '.correct', function(){
        let questionId = localStorage.getItem('currentQuestionId');

        $.ajax({
            url: 'control.php',
            type: "POST",
            data: {
                action: 'correct',
                user_id: $(this)[0].id,
                question_id: questionId
            },
            async: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
            }
        });

        $(this).closest('tr').append($('<td>').append('Done'));
    });

    $(document).on('click', '.wrong', function(){
        let questionId = localStorage.getItem('currentQuestionId');

        $.ajax({
            url: 'control.php',
            type: "POST",
            data: {
                action: 'wrong',
                user_id: $(this)[0].id,
                question_id: questionId
            },
            async: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
            }
        });

        $(this).closest('tr').append($('<td>').append('Done'));
    });
    
    $(document).on('click', '.half', function(){
        let questionId = localStorage.getItem('currentQuestionId');

        $.ajax({
            url: 'control.php',
            type: "POST",
            data: {
                action: 'half',
                user_id: $(this)[0].id,
                question_id: questionId
            },
            async: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
            }
        });

        $(this).closest('tr').append($('<td>').append('Done'));
    });


</script>

</html>