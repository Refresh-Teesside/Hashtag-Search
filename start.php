<!DOCTYPE html>
<html>
<head>

    <title>Refresh Teesside</title>

    <link href="assets/css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="assets/css/custom.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

</head>
<body>


<div class="container">

    <div class="page-header">
        <h1>Hashtag Competition <small><?php echo date('F Y'); ?></small></h1>
    </div>

    <div class="well">
        <h2>Kindle (Thank @audacioushq)</h2>
        <a class="btn btn-primary btn-large" href="http://hashtag.refreshteesside.org/?tag[]=rftees&tag[]=audacious">#rftees #audacious</a>
    </div>

    <hr>

    <div class="well">
        <h2>Free drink (Thank @jamesmills)</h2>
        <a class="btn btn-primary btn-large" href="http://hashtag.refreshteesside.org/?tag[]=rftees&tag[]=beer">#rftees #beer</a>
    </div>

    <hr>

    <div class="well">
        <h2>t-shirt</h2>
        <a class="btn btn-primary btn-large" href="http://hashtag.refreshteesside.org/?tag[]=rftees&tag[]=<?php echo strtolower(date('F')); ?>">#rftees #<?php echo strtolower(date('F')); ?></a>
    </div>

</div>

</body>
</html>