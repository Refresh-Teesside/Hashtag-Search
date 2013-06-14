# Twitter Hashtag Search Competition

This is a simple PHP script which will search Twitter for the given hashtag/hashtags. It will create a list of qualifying Tweets. A qualifiying
tweet is one that is not a retweet and one that has not been tweeted from a blocked user.

You set teh hashtag to search for and the blocked users in the URL
eg: localhost/index.php?tag[]=rftees&tag[]=testing&block[]=jamesmills

The script will pick a winner. The winner will be displayed on screen in a popup after a few seconds.

The script will send a Tweet, in reply to the original, to all the people with a qualifying Tweet entry.
eg: '#rftees #mysupplementbox @jmesmills You were entered into the competition.'

The script will send a Tweet to the winner as well as displaying them on the screen.
eg: 'The WINNER for competition #rftees #mysupplementbox is @jmesmills Congratulations!'

![Example](https://raw.github.com/Refresh-Teesside/Hashtag-Search/master/assets/example/hashtag_one.png)

![Example](https://raw.github.com/Refresh-Teesside/Hashtag-Search/master/assets/example/hashtag_two.png)
