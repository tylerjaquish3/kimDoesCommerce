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
    <link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- bootstrap.min css -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Animate.css -->
    <!-- <link rel="stylesheet" href="css/animate.css"> -->

	<!-- Main Stylesheet -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">

</head>
<body>

    <div class="row">
        <div class="col-xs-12 text-center">
                <form method="POST" action="/trivia/handle.php">
                    <h1>Welcome!</h1>
                    <h3>What is your name?</h3>
                    <input type="hidden" name="action" value="register">
                    <input type="text" name="name-input">
                    <br />
                    <button type="submit" class="btn btn-primary">Ready</button>
                </form>
            </div>
        </div>
    </div>

</body>

<!-- Main jQuery -->
<script type="text/javascript" src="js/jquery.min.js"></script>
<!-- Bootstrap 3.1 -->
<script type="text/javascript" src="js/bootstrap.min.js"></script>

<script type="text/javascript">


</script>

</html>