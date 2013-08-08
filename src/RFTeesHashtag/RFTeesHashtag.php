<?php

namespace RFTeesHashtag;

use Guzzle;

class RFTeesHashtag
{
    public $tags = array();
    public $blocked = array();
    public $twitter_oauth_config = array();
    public $allEntries = array();
    public $validEntries = array();
    public $notValidEntries = array();
    public $totalCompEntries = 0;
    public $winner = array();

    public function __construct($twitter_oauth_config)
    {
        $this->twitter_oauth_config = json_decode($twitter_oauth_config, true);
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function setBlockedUsers($usernames)
    {
        if (!empty($usernames)) {
            $this->blocked = $usernames;
        }
    }

    public function doSearch()
    {
        $query = implode('+AND+%23', $this->tags);
        $client = new Guzzle\Http\Client('https://api.twitter.com/{version}', array('version' => '1.1'));
        $client->addSubscriber(new Guzzle\Plugin\Oauth\OauthPlugin($this->twitter_oauth_config));
        $request = $client->get('search/tweets.json?q=%23' . $query . '&include_entities=true&rpp=100');
        $this->setResults($request->send()->json());
    }

    public function getTotalCompEntries()
    {
        return $this->totalCompEntries;
    }

    private function setResults($response)
    {
        foreach ($response['statuses'] as $result) {

            if (
                (!$result['retweeted_status']) // Cannot be a retweet
                && (!in_array($result['user']['screen_name'], $this->blocked)) // Cannot be a person we have blocked
                // && (empty($result['in_reply_to_status_id '])) // Cannot be a reply to someone elses original tweet
            ) {
                $result['valid'] = 'yes';
                $this->validEntries[] = $result;
                $this->totalCompEntries++;
            } else {
                $this->notValidEntries[] = $result;
                $result['valid'] = 'no';
            }
            $this->allEntries[] = $result;
        }
    }

    public function pickWinner()
    {
        $this->winner = $this->validEntries[array_rand($this->validEntries, 1)];
        return $this->winner;
    }

    public function sendTweet()
    {
        foreach ($this->validEntries as $entry) {

            $client = new Guzzle\Http\Client('https://api.twitter.com/{version}', array('version' => '1.1'));
            $client->addSubscriber(new Guzzle\Plugin\Oauth\OauthPlugin($this->twitter_oauth_config));
            $request = $client->post('statuses/update.json', null, array(
                    'status' => '@' . $entry['user']['screen_name'] . ' You were entered into the competition.' . ' #' . implode(' #', $this->tags),
                    'in_reply_to_status_id' => $entry['id_str']
                ));
            $request->send()->json();

            exit;

        }
    }

    public function tweetWinner()
    {
        $client = new Guzzle\Http\Client('https://api.twitter.com/{version}', array('version' => '1.1'));
        $client->addSubscriber(new Guzzle\Plugin\Oauth\OauthPlugin($this->twitter_oauth_config));
        $request = $client->post('statuses/update.json', null, array(
                'status' => 'The WINNER for competition #' . implode(' #', $this->tags) . ' is @' . $this->winner['user']['screen_name'] . ' Congratulations!'
            ));
        $request->send()->json();
    }
}
