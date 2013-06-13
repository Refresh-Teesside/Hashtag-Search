<?php

require_once 'vendor/autoload.php';
use Guzzle\Http\Client;

include_once 'settings.php';

//error_reporting(0);

if (!$_GET['tag']) {
    echo 'Please provide a tag (?tag[]=rftees&tag[]=buymeabeer)';
    exit;
}

if ($_GET['block']) {
    $block  = $_GET['block'];
} else {
    $block = array();
}

?>
<!DOCTYPE html>
<html>
<head>

    <title>Refresh Teesside</title>

    <link href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" rel="stylesheet" media="screen">

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>

    <script type="text/javascript">

        delayedAlert();

        function delayedAlert() {
            timeoutID = window.setTimeout(showWinner, 10000);
        }

        function showWinner() {
            $(function() {
                var elements = $(".media"),
                index = Math.floor( Math.random() * elements.length ),
                item = elements.eq(index);
                $("#winner").html("<div class=\"media alert alert-success\">" + item.html() + "</div>");
                $("#winner").removeClass("loading");
            });
        }

    </script>

    <style type="text/css">
        body {
            padding: 18px;
        }

        .loading {
            background: url("ajax-loader.gif") no-repeat center;
            height: 100px;
        }
        p.tweet {
            font-size: 20px;
            line-height: 24px;
        }
        .media.entry {
            /*margin-left: -9999px;*/
        }
        .screen-entry {
            float:left;
            margin:4px;
            text-align:center;
            padding: 8px;
        }
        .screen-entry img {
            margin-bottom: 6px;
        }
    </style>

</head>
<body>

    <div id="winner" class="loading"></div>

<?php

    $query = implode('+AND+%23', $_GET['tag']);

    $client = new Client('https://api.twitter.com/{version}', array(
        'version' => '1.1'
    ));
    $client->addSubscriber(new Guzzle\Plugin\Oauth\OauthPlugin($twitter_oauth_settings));
    $request = $client->get('search/tweets.json?q=%23' . $query . '&include_entities=true&rpp=100');
    $response = $request->send()->json();

    foreach ($response['statuses'] as $result) {

        if ((!$result['retweeted_status']) && (!in_array($result['user']['screen_name'], $block))) {
            $results[] = $result;
        }
    }

    echo '<div class="page-header"><h1>There are ' . count($results) . ' valid entries!</h1></div>';

    foreach ($response['statuses'] as $result) {

        if ((!$result['retweeted_status']) && (!in_array($result['user']['screen_name'], $block))) {
            echo '
            <div class="screen-entry alert alert-success">
                <img src="' . $result['user']['profile_image_url'] . '"><br>
                <span class="badge badge-success">@' . $result['user']['screen_name'] . '</span>
            </div>
            ';
         } else {
            echo '
            <div class="screen-entry alert alert-error">
                <img src="' . $result['user']['profile_image_url'] . '"><br>
                <span class="badge badge-important">@' . $result['user']['screen_name'] . '</span>
            </div>
            ';
        }

    }

    echo '<div style="clear:both; height:20px;"></div>';
    echo '<div class="page-header"><h1>Full Tweets</h1></div>';

?>

<?php foreach ($response['statuses'] as $result) { ?>

    <div class="media entry">
        <a class="pull-left" href="#">
            <img class="media-object" src="<?php echo $result['user']['profile_image_url']; ?>">
        </a>

        <div class="media-body">
            <h4 class="media-heading"><?php echo $result['user']['screen_name']; ?></h4>
            <p class="tweet"><?php echo $result['text']; ?></p>
            <p>
                <span class="badge"><?php echo $result['created_at']; ?></span>
                <?php
                    foreach ($result['entities']['hashtags'] as $hashtag) {
                        echo '<span class="badge">#' . $hashtag['text'] . '</span> ';
                    }
                ?>
            </p>
        </div><!-- /.media-body -->
    </div><!-- /.media entry -->

<?php } ?>

</body>
</html>