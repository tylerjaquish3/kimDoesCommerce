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
                <h1>Create Quiz</h1>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-xs-12">
                
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
                    .append($('<td>').append('<a class="btn btn-success correct" id="'+item.user_id+'">Correct</a><a class="btn btn-danger wrong" id="'+item.user_id+'">Wrong</a>'))
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


</script>

</html>