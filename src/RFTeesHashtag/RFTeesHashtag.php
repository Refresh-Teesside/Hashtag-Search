<?php

namespace RFTeesHashtag;

use Guzzle;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class RFTeesHashtag
{
    public $tags = array();
    public $blocked = array();
    public $twitter_oauth_config = array();
    public $allEntries = array();
    public $validEntries = array();
    public $notValidEntries = array();
    public $winner = array();
    public $log;

    public function __construct($twitter_oauth_config)
    {

        $this->log = new Logger('hashtag');
        $this->log->pushHandler(new StreamHandler('logs/' . date('Y-m-d') . '.log'));
        $this->log->addInfo('Running');

        $this->twitter_oauth_config = json_decode($twitter_oauth_config, true);
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
        $this->log->addInfo('Setting tags #' . implode(' #', $this->tags));
    }

    public function setBlockedUsers(Array $usernames)
    {
        if (!empty($usernames)) {
            $this->blocked = array_merge($this->blocked, $usernames);
            $this->log->addInfo('Adding blocked users @' . implode(' @', $usernames));
        }
    }

    public function doSearch()
    {
        $this->log->addInfo('Running search');

        try {

            $query = implode('+AND+%23', $this->tags);
            $client = new Guzzle\Http\Client('https://api.twitter.com/{version}', array('version' => '1.1'));
            $client->addSubscriber(new Guzzle\Plugin\Oauth\OauthPlugin($this->twitter_oauth_config));
            $request = $client->get('search/tweets.json?q=%23' . $query . '&include_entities=true&rpp=100');
            $this->setResults($request->send()->json());

        } catch (Exception $e) {

            $this->log->addError('Error running the search');
            $this->log->addError('Exception ' . $e->getMessage());

        }
    }

    public function getValidEntriesCount()
    {
        return count($this->validEntries);
    }

    public function getNotValidEntriesCount()
    {
        return count($this->notValidEntries);
    }

    public function getAllEntriesCount()
    {
        return count($this->allEntries);
    }

    private function setResults($response)
    {
        $this->log->addInfo('Setting results and splitting into valid, not valid and all.');

        if (count($response['statuses']) > 0) {

            foreach ($response['statuses'] as $result) {
                if (
                    (!$result['retweeted_status']) // Cannot be a retweet
                    && (!in_array($result['user']['screen_name'], $this->blocked)) // Cannot be a person we have blocked
                    // && (empty($result['in_reply_to_status_id '])) // Cannot be a reply to someone elses original tweet
                ) {
                    $result['valid'] = 'yes';
                    $this->validEntries[] = $result;
                } else {
                    $result['valid'] = 'no';
                    $this->notValidEntries[] = $result;
                }
                $this->allEntries[] = $result;
            }

            $this->log->addInfo('Total entries ' . $this->getAllEntriesCount());
            $this->log->addInfo('Total valid entries ' . $this->getValidEntriesCount());
            $this->log->addInfo('Total not valid entries ' . $this->getNotValidEntriesCount());

            if ($this->getValidEntriesCount() <= 0) {
                $this->log->addError('No valid results.');
                echo 'No valid results.';
                throw new \Exception('No valid results.');
            }

        } else {

            $this->log->addError('No results for the search.');
            echo 'No results for the search.';
            throw new \Exception('No results for the search.');

        }
    }

    public function pickWinner()
    {
        $this->log->addInfo('Picking winner');
        $this->winner = $this->validEntries[array_rand($this->validEntries, 1)];
        $this->log->addInfo('Winner is @' . $this->winner['user']['screen_name']);
        return $this->winner;
    }

    public function sendTweet()
    {
        foreach ($this->validEntries as $entry) {

            $this->log->addInfo('Sending tweet to @' . $entry['user']['screen_name']);

            try {

                $client = new Guzzle\Http\Client('https://api.twitter.com/{version}', array('version' => '1.1'));
                $client->addSubscriber(new Guzzle\Plugin\Oauth\OauthPlugin($this->twitter_oauth_config));
                $request = $client->post(
                    'statuses/update.json',
                    null,
                    array(
                        'status' => ' @' . $entry['user']['screen_name'] . ' You were entered into the competition.' . ' #' . implode(' #', $this->tags),
                        'in_reply_to_status_id' => $entry['id_str']
                    )
                );
                $request->send()->json();

            } catch (Exception $e) {

                $this->log->addError('Error sending tweet to @' . $entry['user']['screen_name']);
                $this->log->addError('Exception ' . $e->getMessage());

            }
        }
    }

    public function tweetWinner()
    {
        $this->log->addInfo('Sending tweet to WINNER @' . $this->winner['user']['screen_name']);

        try {

            $client = new Guzzle\Http\Client('https://api.twitter.com/{version}', array('version' => '1.1'));
            $client->addSubscriber(new Guzzle\Plugin\Oauth\OauthPlugin($this->twitter_oauth_config));
            $request = $client->post(
                'statuses/update.json',
                null,
                array(
                    'status' => 'The WINNER for competition #' . implode(' #', $this->tags) . ' is @' . $this->winner['user']['screen_name'] . ' Congratulations!'
                )
            );
            $request->send()->json();

        } catch (Exception $e) {

            $this->log->addError('Error sending tweet to WINNER @' . $this->winner['user']['screen_name']);
            $this->log->addError('Exception ' . $e->getMessage());

        }
    }
}
