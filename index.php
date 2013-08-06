<?php

//error_reporting(0);

require_once 'vendor/autoload.php';

$RFTeesHashtag = new \RFTeesHashtag\RFTeesHashtag(file_get_contents(__DIR__ . '/config/settings.json'));
$RFTeesHashtag->check();
$RFTeesHashtag->setTags($_GET['tag']);
$RFTeesHashtag->setBlockedUsers($_GET['block']);
$RFTeesHashtag->doSearch();
$RFTeesHashtag->pickWinner();
//$RFTeesHashtag->sendTweet();
$RFTeesHashtag->tweetWinner();

function formatDate($date)
{
    return date('l jS M Y g:ia', strtotime($date));
}

?>
<!DOCTYPE html>
<html>
<head>

    <title>Refresh Teesside</title>

    <link href="assets/css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="assets/css/custom.css" rel="stylesheet" media="screen">

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/custom.js"></script>

</head>
<body>


<div class="container">

    <div class="loading"></div>

    <div id="winnerModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <h3 id="myModalLabel"><?php echo $RFTeesHashtag->winner['user']['name']; ?></h3>
        </div>
        <div class="modal-body">
            <div class="media entry">
                <a class="pull-left" href="#">
                    <img class="media-object" src="<?php echo $RFTeesHashtag->winner['user']['profile_image_url']; ?>">
                </a>

                <div class="media-body">
                    <h4 class="media-heading"><?php echo $RFTeesHashtag->winner['user']['name']; ?> (@<?php echo $RFTeesHashtag->winner['user']['screen_name']; ?>)</h4>
                    <p class="tweet"><?php echo $RFTeesHashtag->winner['text']; ?></p>
                    <p>
                        <span class="badge"><?php echo formatDate($RFTeesHashtag->winner['created_at']); ?></span>
                        <?php echo ($RFTeesHashtag->winner['favorited'] > 0) ? '<span class="badge">Favorited:' . $RFTeesHashtag->winner['favorited'] . '</span>' : '' ; ?>
                        <?php echo ($RFTeesHashtag->winner['retweeted'] > 0) ? '<span class="badge">Retweeted:' . $RFTeesHashtag->winner['retweeted'] . '</span>' : '' ; ?>
                        <?php
                        foreach ($RFTeesHashtag->winner['entities']['hashtags'] as $hashtag) {
                            echo '<span class="badge badge-info">#' . $hashtag['text'] . '</span> ';
                        }
                        ?>
                    </p>
                </div><!-- /.media-body -->
            </div><!-- /.media entry -->
        </div>
    </div>

    <div class="page-header"><h1><?php echo $RFTeesHashtag->getTotalCompEntries();?> valid entries!</h1></div>

    <?php

        foreach ($RFTeesHashtag->tags as $tag) {
            echo '<span class="btn btn-large btn-primary">#' . $tag . '</span> ';
        }
    echo '<hr />';

    ?>

    <?php

        foreach ($RFTeesHashtag->allEntries as $result) {

            if ($result['valid'] === 'yes') {
                $alert = 'success';
                $badge = 'success';
            } else {
                $alert = 'error';
                $badge = 'important';
            }
            echo '
                <div class="screen-entry alert alert-' . $alert . '">
                    <img src="' . $result['user']['profile_image_url'] . '"><br>
                    <span class="badge badge-' . $badge . '">@' . $result['user']['screen_name'] . '</span>
                </div>
            ';

        }
    ?>

    <div style="clear:both; height:20px;"></div>

    <div class="page-header"><h1>All Tweets</h1></div>

    <?php foreach ($RFTeesHashtag->allEntries as $result) { ?>

        <div class="media entry">
            <a class="pull-left" href="#">
                <img class="media-object" src="<?php echo $result['user']['profile_image_url']; ?>">
            </a>

            <div class="media-body">
                <h4 class="media-heading"><?php echo $result['user']['name']; ?> (@<?php echo $result['user']['screen_name']; ?>)</h4>
                <p class="tweet"><?php echo $result['text']; ?></p>
                <p>
                    <span class="badge"><?php echo formatDate($result['created_at']); ?></span>
                    <?php echo ($result['favorited'] > 0) ? '<span class="badge">Favorited:' . $result['favorited'] . '</span>' : '' ; ?>
                    <?php echo ($$result['retweeted'] > 0) ? '<span class="badge">Retweeted:' . $result['retweeted'] . '</span>' : '' ; ?>
                    <?php
                        foreach ($result['entities']['hashtags'] as $hashtag) {
                            echo '<span class="badge badge-info">#' . $hashtag['text'] . '</span> ';
                        }
                    ?>
                </p>
            </div><!-- /.media-body -->
        </div><!-- /.media entry -->

    <?php } ?>

</div>

</body>
</html>