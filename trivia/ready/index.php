<?php
    include '../datalogin.php';
    if (empty($_GET) && !isset($_GET['id'])) {
        header("Location: ".URL);
	    exit;
    }
?>
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

    <div class="row text-center" id="question-row">

        <div class="col-xs-8">
            <h1 id="question">
                Please wait for the round to start
            </h1>
            <div id="answer">
                <form>
                    <h3 id="question_num_stuff" style="display:none">Question #<span id="question_num"></span></h3>
                    <input type="text" id="answer-input">
                    <input type="hidden" id="question_id">
                    <button class="btn btn-primary" id="submit-answer">Submit</button>
                </form>
            </div>

        </div>
        <div class="col-xs-4">
            <div id="timer">
                <div class="progress vertical">
                    <div class="progress-bar" id="bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:100%"></div>
                </div>
            </div>
        </div>

    </div>

    <div class="row text-center" id="game-over-row" style="display: none;">
        <div class="col-xs-12">
            <h1>Game Over!</h1>
        </div>
    </div>

    <div class="row text-center" id="answers-row" style="display: none;">
        <div class="col-xs-12">
            <h3 id="response"></h3>
            <table id="current-answers" class="table striped">
                <thead>
                    <th>User</th>
                    <th>Answer</th>
                    <th>Points</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row text-center" id="standings-row">
        <div class="col-xs-12">
            <h3>Standings</h3>
            <table id="standings" class="table striped">
                <thead>
                    <th>Rank</th>
                    <th>User</th>
                    <th>Points</th>
                </thead>
                <tbody>
                </tbody>
            </table>
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

    let savedStandings = [];
    var gameOver = "false";

    var $currentAnswersTable = $('#current-answers').DataTable({
         "order": [[ 2, "desc" ]],
        "dom": ''
    });

    var $standingsTable = $('#datatable-standings').DataTable({
        "order": [[ 0, "asc" ]],
        "dom": 'rt<"bottom"ip><"clear">'
    });

    const evtSource = new EventSource("updates.php");

    $('#submit-answer').prop('disabled', true);
    localStorage.setItem('currentQuestionId', 0);

     evtSource.addEventListener("game", function(event) {
        gameOver = JSON.parse(event.data).gameOver;
        if (gameOver == "true") {
            $('#question-row').hide();
            $('#answers-row').hide();
            $('#game-over-row').show();
        }
    });

    evtSource.addEventListener("question", function(event) {
        var questionId = JSON.parse(event.data).id;
        var question = JSON.parse(event.data).question;
        var answer = JSON.parse(event.data).answer;
        var sortOrder = JSON.parse(event.data).sort_order;
        var allAnswers = JSON.parse(event.data).allAnswers;

        if (gameOver == "false") {
            if (answer) {
                message = "Correct answer is: <br />"+answer;
                $('#response').html(message);

                $('#answers-row').show();
                $('#current-answers tbody').empty();

                allAnswers = allAnswers.players;
                allAnswers.forEach(function (item) {
                    if (item.correct == 0) { item.points = 0; }
                    $("#current-answers").find('tbody')
                        .append($('<tr>')
                            .append($('<td>').append(item.name))
                            .append($('<td>').append(item.answer))
                            .append($('<td>').append(item.points))
                        );
                });
            }

            currentQuestionId = localStorage.getItem('currentQuestionId');


            // New question was launced
            if (currentQuestionId != questionId) {
                $('#question').html(question);
                $('#question_id').val(questionId);
                $('#question_num_stuff').show();
                $('#question_num').html(sortOrder);
                $('#submit-answer').prop('disabled', false);
                $('#answer-input').val('');
                $('#response').html('');
                $('#standings tbody').empty();
                $('#answers-row').hide();
                $('#current-answers tbody').empty();

                localStorage.setItem('currentQuestionId', questionId);
                savedStandings = [];
            }
        }
    });

    evtSource.addEventListener("timer", function(event) {
        var id = JSON.parse(event.data).id;
        var time = JSON.parse(event.data).time;

        if (time > 0) {
            $('#bar').css('width', time+'%');
        } else {
            $('#question').html('Time is up!');
            $('#bar').css('width', '0%');

            // button disabled
            $('#submit-answer').prop('disabled', true);
        }
    });

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

    $('#submit-answer').click(function (e) {
        e.preventDefault();

        if ($('#answer-input').val() == '') {
            alert('You can\'t get any points if your answer is blank!');
        } else {
            $.ajax({
                url: '../handle.php',
                type: "POST",
                data: {
                    action: 'answer-submit',
                    user_id: "<?php echo $_GET['id']; ?>",
                    question_id: $('#question_id').val(),
                    answer: $('#answer-input').val()
                },
                // async: false,
                dataType: 'json',
                complete: function (response) {

                    // show max points for correct answer
                    let maxPoints = response.responseJSON.max_points;
                    let message = 'Your answer is being reviewed. <br />Your max points for this round is: '+maxPoints;

                    $('#question').html(message);
                    $('#submit-answer').prop('disabled', true);
                }
            });
        }
    })

</script>

</html>