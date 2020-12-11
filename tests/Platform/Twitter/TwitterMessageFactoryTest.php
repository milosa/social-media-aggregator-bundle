<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Tests\Platform\Twitter;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter\TwitterMessage;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter\TwitterMessageFactory;
use PHPUnit\Framework\TestCase;

class TwitterMessageFactoryTest extends TestCase
{
    private $sampleTweetJson = [
        'tweet_simple' => '{"created_at":"Tue Aug 28 20:14:58 +0000 2018","id":1034534829918806016,"id_str":"1034534829918806016","full_text":"The people who do the work are the crafts-men and -women who are the true carriers of the agile torch.","truncated":false,"display_text_range":[0,102],"entities":{"hashtags":[],"symbols":[],"user_mentions":[],"urls":[]},"source":"\u003ca href=\"http:\/\/twitter.com\/download\/iphone\" rel=\"nofollow\"\u003eTwitter for iPhone\u003c\/a\u003e","in_reply_to_status_id":1034532580798787584,"in_reply_to_status_id_str":"1034532580798787584","in_reply_to_user_id":9505092,"in_reply_to_user_id_str":"9505092","in_reply_to_screen_name":"unclebobmartin","user":{"id":9505092,"id_str":"9505092","name":"Uncle Bob Martin","screen_name":"unclebobmartin","location":"iPhone: 56.802658,9.868149","description":"Software Craftsman","url":"http:\/\/t.co\/cEFTI5CDhO","entities":{"url":{"urls":[{"url":"http:\/\/t.co\/cEFTI5CDhO","expanded_url":"http:\/\/www.cleancoder.com","display_url":"cleancoder.com","indices":[0,22]}]},"description":{"urls":[]}},"protected":false,"followers_count":112531,"friends_count":303,"listed_count":4026,"created_at":"Wed Oct 17 19:03:34 +0000 2007","favourites_count":529,"utc_offset":null,"time_zone":null,"geo_enabled":true,"verified":false,"statuses_count":22559,"lang":"en","contributors_enabled":false,"is_translator":false,"is_translation_enabled":false,"profile_background_color":"DFE1E1","profile_background_image_url":"http:\/\/abs.twimg.com\/images\/themes\/theme1\/bg.png","profile_background_image_url_https":"https:\/\/abs.twimg.com\/images\/themes\/theme1\/bg.png","profile_background_tile":false,"profile_image_url":"http:\/\/pbs.twimg.com\/profile_images\/1102364992\/clean_code_72_color_normal.png","profile_image_url_https":"https:\/\/pbs.twimg.com\/profile_images\/1102364992\/clean_code_72_color_normal.png","profile_link_color":"0000FF","profile_sidebar_border_color":"0B6900","profile_sidebar_fill_color":"FFBCBC","profile_text_color":"8F0707","profile_use_background_image":true,"has_extended_profile":false,"default_profile":false,"default_profile_image":false,"following":false,"follow_request_sent":false,"notifications":false,"translator_type":"none"},"geo":null,"coordinates":null,"place":null,"contributors":null,"is_quote_status":false,"retweet_count":15,"favorite_count":66,"favorited":false,"retweeted":false,"lang":"en"}',
        'tweet_with_hashtags_and_image' => '{"created_at":"Wed Aug 15 19:27:47 +0000 2018","id":1029811912605724672,"id_str":"1029811912605724672","full_text":"Happy #NationalRelaxationDay to the king of doing absolutely nothing. #FamilyGuy https:\/\/t.co\/VWsijOI1ff","truncated":false,"display_text_range":[0,82],"entities":{"hashtags":[{"text":"NationalRelaxationDay","indices":[6,28]},{"text":"FamilyGuy","indices":[72,82]}],"symbols":[],"user_mentions":[],"urls":[],"media":[{"id":1029811829432758272,"id_str":"1029811829432758272","indices":[83,106],"media_url":"http:\/\/pbs.twimg.com\/media\/DkqgZxbVsAAt-6s.jpg","media_url_https":"https:\/\/pbs.twimg.com\/media\/DkqgZxbVsAAt-6s.jpg","url":"https:\/\/t.co\/VWsijOI1ff","display_url":"pic.twitter.com\/VWsijOI1ff","expanded_url":"https:\/\/twitter.com\/FamilyGuyonFOX\/status\/1029811912605724672\/photo\/1","type":"photo","sizes":{"thumb":{"w":150,"h":150,"resize":"crop"},"small":{"w":680,"h":383,"resize":"fit"},"large":{"w":2000,"h":1125,"resize":"fit"},"medium":{"w":1200,"h":675,"resize":"fit"}}}]},"extended_entities":{"media":[{"id":1029811829432758272,"id_str":"1029811829432758272","indices":[83,106],"media_url":"http:\/\/pbs.twimg.com\/media\/DkqgZxbVsAAt-6s.jpg","media_url_https":"https:\/\/pbs.twimg.com\/media\/DkqgZxbVsAAt-6s.jpg","url":"https:\/\/t.co\/VWsijOI1ff","display_url":"pic.twitter.com\/VWsijOI1ff","expanded_url":"https:\/\/twitter.com\/FamilyGuyonFOX\/status\/1029811912605724672\/photo\/1","type":"photo","sizes":{"thumb":{"w":150,"h":150,"resize":"crop"},"small":{"w":680,"h":383,"resize":"fit"},"large":{"w":2000,"h":1125,"resize":"fit"},"medium":{"w":1200,"h":675,"resize":"fit"}}}]},"source":"\u003ca href=\"https:\/\/studio.twitter.com\" rel=\"nofollow\"\u003eMedia Studio\u003c\/a\u003e","in_reply_to_status_id":null,"in_reply_to_status_id_str":null,"in_reply_to_user_id":null,"in_reply_to_user_id_str":null,"in_reply_to_screen_name":null,"user":{"id":32625314,"id_str":"32625314","name":"Family Guy","screen_name":"FamilyGuyonFOX","location":"@FOXTV","description":"OFFICIAL TWITTER FOR #FamilyGuy. We\'re back for Season 17 on September 30!","url":"https:\/\/t.co\/9fLuVkqjaz","entities":{"url":{"urls":[{"url":"https:\/\/t.co\/9fLuVkqjaz","expanded_url":"http:\/\/fox.tv\/Watchfg","display_url":"fox.tv\/Watchfg","indices":[0,23]}]},"description":{"urls":[]}},"protected":false,"followers_count":1890589,"friends_count":783,"listed_count":3150,"created_at":"Fri Apr 17 22:24:06 +0000 2009","favourites_count":5687,"utc_offset":null,"time_zone":null,"geo_enabled":true,"verified":true,"statuses_count":14056,"lang":"en","contributors_enabled":false,"is_translator":false,"is_translation_enabled":false,"profile_background_color":"003F8A","profile_background_image_url":"http:\/\/abs.twimg.com\/images\/themes\/theme1\/bg.png","profile_background_image_url_https":"https:\/\/abs.twimg.com\/images\/themes\/theme1\/bg.png","profile_background_tile":false,"profile_image_url":"http:\/\/pbs.twimg.com\/profile_images\/917922823028346880\/ClMRZwyy_normal.jpg","profile_image_url_https":"https:\/\/pbs.twimg.com\/profile_images\/917922823028346880\/ClMRZwyy_normal.jpg","profile_banner_url":"https:\/\/pbs.twimg.com\/profile_banners\/32625314\/1530599101","profile_link_color":"F0750A","profile_sidebar_border_color":"FFFFFF","profile_sidebar_fill_color":"FCE366","profile_text_color":"333333","profile_use_background_image":true,"has_extended_profile":false,"default_profile":false,"default_profile_image":false,"following":false,"follow_request_sent":false,"notifications":false,"translator_type":"none"},"geo":null,"coordinates":null,"place":null,"contributors":null,"is_quote_status":false,"retweet_count":334,"favorite_count":1318,"favorited":false,"retweeted":false,"possibly_sensitive":false,"possibly_sensitive_appealable":false,"lang":"en"}',
        'tweet_with_hashtags_and_mentions' => '{"created_at":"Sat Jul 21 21:20:21 +0000 2018","id":1020780545515507712,"id_str":"1020780545515507712","full_text":"The joke answer is money. The comfort and stability this show has offered me is amazing and thats all thanks to you the fans. @alexborstein on what its like having #FamilyGuy on the air for 20 years.#SDCC","truncated":false,"display_text_range":[0,209],"entities":{"hashtags":[{"text":"FamilyGuy","indices":[168,178]},{"text":"SDCC","indices":[204,209]}],"symbols":[],"user_mentions":[{"screen_name":"AlexBorstein","name":"Alex Borstein","id":616616825,"id_str":"616616825","indices":[129,142]}],"urls":[]},"source":"\u003ca href=\"http:\/\/twitter.com\" rel=\"nofollow\"\u003eTwitter Web Client\u003c\/a\u003e","in_reply_to_status_id":null,"in_reply_to_status_id_str":null,"in_reply_to_user_id":null,"in_reply_to_user_id_str":null,"in_reply_to_screen_name":null,"user":{"id":32625314,"id_str":"32625314","name":"Family Guy","screen_name":"FamilyGuyonFOX","location":"@FOXTV","description":"OFFICIAL TWITTER FOR #FamilyGuy. We\'re back for Season 17 on September 30!","url":"https:\/\/t.co\/9fLuVkqjaz","entities":{"url":{"urls":[{"url":"https:\/\/t.co\/9fLuVkqjaz","expanded_url":"http:\/\/fox.tv\/Watchfg","display_url":"fox.tv\/Watchfg","indices":[0,23]}]},"description":{"urls":[]}},"protected":false,"followers_count":1890589,"friends_count":783,"listed_count":3150,"created_at":"Fri Apr 17 22:24:06 +0000 2009","favourites_count":5687,"utc_offset":null,"time_zone":null,"geo_enabled":true,"verified":true,"statuses_count":14056,"lang":"en","contributors_enabled":false,"is_translator":false,"is_translation_enabled":false,"profile_background_color":"003F8A","profile_background_image_url":"http:\/\/abs.twimg.com\/images\/themes\/theme1\/bg.png","profile_background_image_url_https":"https:\/\/abs.twimg.com\/images\/themes\/theme1\/bg.png","profile_background_tile":false,"profile_image_url":"http:\/\/pbs.twimg.com\/profile_images\/917922823028346880\/ClMRZwyy_normal.jpg","profile_image_url_https":"https:\/\/pbs.twimg.com\/profile_images\/917922823028346880\/ClMRZwyy_normal.jpg","profile_banner_url":"https:\/\/pbs.twimg.com\/profile_banners\/32625314\/1530599101","profile_link_color":"F0750A","profile_sidebar_border_color":"FFFFFF","profile_sidebar_fill_color":"FCE366","profile_text_color":"333333","profile_use_background_image":true,"has_extended_profile":false,"default_profile":false,"default_profile_image":false,"following":false,"follow_request_sent":false,"notifications":false,"translator_type":"none"},"geo":null,"coordinates":null,"place":null,"contributors":null,"is_quote_status":false,"retweet_count":25,"favorite_count":145,"favorited":false,"retweeted":false,"lang":"en"}',
        'tweet_with_retweet' => '{"created_at":"Tue Aug 28 14:30:24 +0000 2018","id":1034448117028945920,"id_str":"1034448117028945920","full_text":"RT @danielbmarkham: @unclebobmartin We create tests to have conversations that determine value. Sometimes those tests are in the form of de","truncated":false,"display_text_range":[0,140],"entities":{"hashtags":[],"symbols":[],"user_mentions":[{"screen_name":"danielbmarkham","name":"Daniel Markham","id":55648990,"id_str":"55648990","indices":[3,18]},{"screen_name":"unclebobmartin","name":"Uncle Bob Martin","id":9505092,"id_str":"9505092","indices":[20,35]}],"urls":[]},"source":"\u003ca href=\"http:\/\/twitter.com\/download\/iphone\" rel=\"nofollow\"\u003eTwitter for iPhone\u003c\/a\u003e","in_reply_to_status_id":null,"in_reply_to_status_id_str":null,"in_reply_to_user_id":null,"in_reply_to_user_id_str":null,"in_reply_to_screen_name":null,"user":{"id":9505092,"id_str":"9505092","name":"Uncle Bob Martin","screen_name":"unclebobmartin","location":"iPhone: 56.802658,9.868149","description":"Software Craftsman","url":"http:\/\/t.co\/cEFTI5CDhO","entities":{"url":{"urls":[{"url":"http:\/\/t.co\/cEFTI5CDhO","expanded_url":"http:\/\/www.cleancoder.com","display_url":"cleancoder.com","indices":[0,22]}]},"description":{"urls":[]}},"protected":false,"followers_count":112531,"friends_count":303,"listed_count":4026,"created_at":"Wed Oct 17 19:03:34 +0000 2007","favourites_count":529,"utc_offset":null,"time_zone":null,"geo_enabled":true,"verified":false,"statuses_count":22559,"lang":"en","contributors_enabled":false,"is_translator":false,"is_translation_enabled":false,"profile_background_color":"DFE1E1","profile_background_image_url":"http:\/\/abs.twimg.com\/images\/themes\/theme1\/bg.png","profile_background_image_url_https":"https:\/\/abs.twimg.com\/images\/themes\/theme1\/bg.png","profile_background_tile":false,"profile_image_url":"http:\/\/pbs.twimg.com\/profile_images\/1102364992\/clean_code_72_color_normal.png","profile_image_url_https":"https:\/\/pbs.twimg.com\/profile_images\/1102364992\/clean_code_72_color_normal.png","profile_link_color":"0000FF","profile_sidebar_border_color":"0B6900","profile_sidebar_fill_color":"FFBCBC","profile_text_color":"8F0707","profile_use_background_image":true,"has_extended_profile":false,"default_profile":false,"default_profile_image":false,"following":false,"follow_request_sent":false,"notifications":false,"translator_type":"none"},"geo":null,"coordinates":null,"place":null,"contributors":null,"retweeted_status":{"created_at":"Mon Aug 27 12:29:09 +0000 2018","id":1034055215186509824,"id_str":"1034055215186509824","full_text":"@unclebobmartin We create tests to have conversations that determine value. Sometimes those tests are in the form of design discussions. Sometimes they\'re business discussions. Sometimes executable code. Sometimes UX workshops. There\'s a pattern here across all domains you can learn and apply.","truncated":false,"display_text_range":[16,294],"entities":{"hashtags":[],"symbols":[],"user_mentions":[{"screen_name":"unclebobmartin","name":"Uncle Bob Martin","id":9505092,"id_str":"9505092","indices":[0,15]}],"urls":[]},"source":"\u003ca href=\"http:\/\/twitter.com\" rel=\"nofollow\"\u003eTwitter Web Client\u003c\/a\u003e","in_reply_to_status_id":1034053545950175232,"in_reply_to_status_id_str":"1034053545950175232","in_reply_to_user_id":9505092,"in_reply_to_user_id_str":"9505092","in_reply_to_screen_name":"unclebobmartin","user":{"id":55648990,"id_str":"55648990","name":"Daniel Markham","screen_name":"danielbmarkham","location":"East Coast, United States","description":"I help technology workers lead happier and more productive lives. Wrote the book on making stuff people want wth minimal overhead by optimizing information flow","url":"https:\/\/t.co\/dj2IIEMinX","entities":{"url":{"urls":[{"url":"https:\/\/t.co\/dj2IIEMinX","expanded_url":"https:\/\/leanpub.com\/info-ops","display_url":"leanpub.com\/info-ops","indices":[0,23]}]},"description":{"urls":[]}},"protected":false,"followers_count":1203,"friends_count":350,"listed_count":114,"created_at":"Fri Jul 10 19:57:10 +0000 2009","favourites_count":3328,"utc_offset":null,"time_zone":null,"geo_enabled":true,"verified":false,"statuses_count":14305,"lang":"en","contributors_enabled":false,"is_translator":false,"is_translation_enabled":false,"profile_background_color":"000000","profile_background_image_url":"http:\/\/abs.twimg.com\/images\/themes\/theme1\/bg.png","profile_background_image_url_https":"https:\/\/abs.twimg.com\/images\/themes\/theme1\/bg.png","profile_background_tile":false,"profile_image_url":"http:\/\/pbs.twimg.com\/profile_images\/1024056768375091201\/0RzP6S26_normal.jpg","profile_image_url_https":"https:\/\/pbs.twimg.com\/profile_images\/1024056768375091201\/0RzP6S26_normal.jpg","profile_banner_url":"https:\/\/pbs.twimg.com\/profile_banners\/55648990\/1516817846","profile_link_color":"91D2FA","profile_sidebar_border_color":"000000","profile_sidebar_fill_color":"000000","profile_text_color":"000000","profile_use_background_image":false,"has_extended_profile":false,"default_profile":false,"default_profile_image":false,"following":false,"follow_request_sent":false,"notifications":false,"translator_type":"none"},"geo":null,"coordinates":null,"place":null,"contributors":null,"is_quote_status":false,"retweet_count":6,"favorite_count":12,"favorited":false,"retweeted":false,"lang":"en"},"is_quote_status":false,"retweet_count":6,"favorite_count":0,"favorited":false,"retweeted":false,"lang":"en"}',
    ];

    public function testInvalidJsonThrowsException(): void
    {
        $this->expectExceptionMessage('Invalid JSON');
        $this->expectException(\InvalidArgumentException::class);
        TwitterMessageFactory::createMessage('string');
    }

    public function testCreateSimpleMessage(): void
    {
        $message = TwitterMessageFactory::createMessage($this->sampleTweetJson['tweet_simple']);

        $expected = new TwitterMessage('API', 'twitter.twig');
        $expected->setDate(new \DateTime('2018-08-28 20:14:58'));
        $expected->setBody('The people who do the work are the crafts-men and -women who are the true carriers of the agile torch.');
        $expected->setParsedBody('The people who do the work are the crafts-men and -women who are the true carriers of the agile torch.');
        $expected->setURL('https://twitter.com/statuses/1034534829918806016');
        $expected->setAuthor('Uncle Bob Martin');
        $expected->setScreenName('unclebobmartin');
        $expected->setAuthorURL('https://twitter.com/unclebobmartin');
        $expected->setAuthorDescription('Software Craftsman');
        $expected->setAuthorThumbnail('https://pbs.twimg.com/profile_images/1102364992/clean_code_72_color_normal.png');
        $expected->setNetwork('twitter');

        $this->assertEquals($expected, $message);
    }

    public function testCreateTweetWithHashTagAndImage(): void
    {
        $message = TwitterMessageFactory::createMessage($this->sampleTweetJson['tweet_with_hashtags_and_image']);

        $expected = new TwitterMessage('API', 'twitter.twig');
        $expected->setDate(new \DateTime('2018-08-15 19:27:47'));
        $expected->setBody('Happy #NationalRelaxationDay to the king of doing absolutely nothing. #FamilyGuy https://t.co/VWsijOI1ff');
        $expected->setParsedBody('Happy <a href="https://twitter.com/hashtag/NationalRelaxationDay" rel="noopener noreferrer">#NationalRelaxationDay</a> to the king of doing absolutely nothing. <a href="https://twitter.com/hashtag/FamilyGuy" rel="noopener noreferrer">#FamilyGuy</a> <img src="https://pbs.twimg.com/media/DkqgZxbVsAAt-6s.jpg:thumb"/>');
        $expected->setURL('https://twitter.com/statuses/1029811912605724672');
        $expected->setAuthor('Family Guy');
        $expected->setScreenName('FamilyGuyonFOX');
        $expected->setAuthorURL('https://twitter.com/FamilyGuyonFOX');
        $expected->setAuthorDescription("OFFICIAL TWITTER FOR #FamilyGuy. We're back for Season 17 on September 30!");
        $expected->setAuthorThumbnail('https://pbs.twimg.com/profile_images/917922823028346880/ClMRZwyy_normal.jpg');
        $expected->setNetwork('twitter');

        $this->assertEquals($expected, $message);
    }

    public function testCreateTweetWithHashTagAndMention(): void
    {
        $message = TwitterMessageFactory::createMessage($this->sampleTweetJson['tweet_with_hashtags_and_mentions']);

        $expected = new TwitterMessage('API', 'twitter.twig');
        $expected->setDate(new \DateTime('2018-07-21 21:20:21'));
        $expected->setTemplate('twitter.twig');
        $expected->setBody('The joke answer is money. The comfort and stability this show has offered me is amazing and thats all thanks to you the fans. @alexborstein on what its like having #FamilyGuy on the air for 20 years.#SDCC');
        $expected->setParsedBody('The joke answer is money. The comfort and stability this show has offered me is amazing and thats all thanks to you the fans. <a href="https://twitter.com/alexborstein" rel="noopener noreferrer">@alexborstein</a> on what its like having <a href="https://twitter.com/hashtag/FamilyGuy" rel="noopener noreferrer">#FamilyGuy</a> on the air for 20 years.<a href="https://twitter.com/hashtag/SDCC" rel="noopener noreferrer">#SDCC</a>');
        $expected->setURL('https://twitter.com/statuses/1020780545515507712');
        $expected->setAuthor('Family Guy');
        $expected->setScreenName('FamilyGuyonFOX');
        $expected->setAuthorURL('https://twitter.com/FamilyGuyonFOX');
        $expected->setAuthorDescription("OFFICIAL TWITTER FOR #FamilyGuy. We're back for Season 17 on September 30!");
        $expected->setAuthorThumbnail('https://pbs.twimg.com/profile_images/917922823028346880/ClMRZwyy_normal.jpg');
        $expected->setNetwork('twitter');

        $this->assertEquals($expected, $message);
    }

    public function testCreateTweetWithRetweet(): void
    {
        $message = TwitterMessageFactory::createMessage($this->sampleTweetJson['tweet_with_retweet']);

        $expected = new TwitterMessage('API', 'twitter.twig');
        $expected->setDate(new \DateTime('2018-08-28 14:30:24'));
        $expected->setTemplate('twitter.twig');
        $expected->setBody('RT @danielbmarkham: @unclebobmartin We create tests to have conversations that determine value. Sometimes those tests are in the form of de');
        $expected->setParsedBody('RT <a href="https://twitter.com/danielbmarkham" rel="noopener noreferrer">@danielbmarkham</a>: <a href="https://twitter.com/unclebobmartin" rel="noopener noreferrer">@unclebobmartin</a> We create tests to have conversations that determine value. Sometimes those tests are in the form of de');
        $expected->setURL('https://twitter.com/statuses/1034448117028945920');
        $expected->setAuthor('Uncle Bob Martin');
        $expected->setScreenName('unclebobmartin');
        $expected->setAuthorURL('https://twitter.com/unclebobmartin');
        $expected->setAuthorDescription('Software Craftsman');
        $expected->setAuthorThumbnail('https://pbs.twimg.com/profile_images/1102364992/clean_code_72_color_normal.png');
        $expected->setNetwork('twitter');

        $retweet = new TwitterMessage('API', 'twitter.twig');
        $retweet->setDate(new \DateTime('2018-08-27 12:29:09'));
        $retweet->setTemplate('twitter.twig');
        $retweet->setBody('@unclebobmartin We create tests to have conversations that determine value. Sometimes those tests are in the form of design discussions. Sometimes they\'re business discussions. Sometimes executable code. Sometimes UX workshops. There\'s a pattern here across all domains you can learn and apply.');
        $retweet->setParsedBody('<a href="https://twitter.com/unclebobmartin" rel="noopener noreferrer">@unclebobmartin</a> We create tests to have conversations that determine value. Sometimes those tests are in the form of design discussions. Sometimes they\'re business discussions. Sometimes executable code. Sometimes UX workshops. There\'s a pattern here across all domains you can learn and apply.');
        $retweet->setURL('https://twitter.com/statuses/1034055215186509824');
        $retweet->setAuthor('Daniel Markham');
        $retweet->setScreenName('danielbmarkham');
        $retweet->setAuthorURL('https://twitter.com/danielbmarkham');
        $retweet->setAuthorDescription('I help technology workers lead happier and more productive lives. Wrote the book on making stuff people want wth minimal overhead by optimizing information flow');
        $retweet->setAuthorThumbnail('https://pbs.twimg.com/profile_images/1024056768375091201/0RzP6S26_normal.jpg');
        $retweet->setNetwork('twitter');

        $expected->setRetweet($retweet);

        $this->assertEquals($expected, $message);
    }
}
