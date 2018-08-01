<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites;

use Abraham\TwitterOAuth\TwitterOAuth;
use Milosa\SocialMediaAggregatorBundle\Message;
use Milosa\SocialMediaAggregatorBundle\Parser;
use Milosa\SocialMediaAggregatorBundle\Sites\Fetcher;
use Milosa\SocialMediaAggregatorBundle\Sites\Twitter\TwitterMessage;
use Milosa\SocialMediaAggregatorBundle\Sites\TwitterFetcher;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class TwitterFetcherTest extends FetcherTestCase
{
    use TestDataTrait;

    public function setUp()
    {
        $this->fetcher = $this->getTestFetcher();
    }

    public function testCanCreateFetcher(): void
    {
        $this->assertInstanceOf(TwitterFetcher::class, $this->fetcher);
    }

//    public function testAddsParsers(): void
//    {
//        /**
//         * @var Message
//         */
//        $message = $this->fetcher->getData()[0];
//        $parsers = $message->getParsers();
//
//        $this->assertCount(3, $parsers);
//        $this->assertContainsOnlyInstancesOf(Parser::class, $parsers);
//    }

    public function testGetMessageObjects(): void
    {
        $result = $this->fetcher->getData();

        $this->assertNotNull($result);
        $this->assertCount(2, $result);

        $this->assertInstanceOf(TwitterMessage::class, $result[0]);
        $this->assertInstanceOf(TwitterMessage::class, $result[1]);

        $expected = [];
        $expected[0] = new TwitterMessage();
        $expected[0]->setDate(new \DateTime('2018-01-18 18:47:36'));
        $expected[0]->setTemplate('twitter.twig');
        $expected[0]->setBody('This is a test. http://t.co/rJC5Pxsu and a url https://t.co/XweGngmxlP');
        $expected[0]->setParsedBody('This is a test. <img src="https://p.twimg.com/AZVLmp-CIAAbkyy.jpg:small"/> and a url <a href="https://cards.twitter.com/cards/18ce53wgo4h/3xo1c">cards.twitter.com/cards/18ce53wg…</a>');
        $expected[0]->setURL('https://twitter.com/statuses/850007368138018817');
        $expected[0]->setAuthor('Twitter API');
        $expected[0]->setScreenName('twitterapi');
        $expected[0]->setAuthorURL('https://twitter.com/twitterapi');
        $expected[0]->setAuthorDescription("The Real Twitter API. I tweet about API changes, service issues and happily answer questions about Twitter and our API. Don't get an answer? It's on my website.");
        $expected[0]->setAuthorThumbnail('https://pbs.twimg.com/profile_images/2284174872/7df3h38zabcvjylnyfe3_normal.png');
        $expected[0]->setRetweet($this->getFirstRetweet());

        $expected[1] = new TwitterMessage();
        $expected[1]->setTemplate('twitter.twig');
        $expected[1]->setDate(new \DateTime('2018-01-18 07:38:10'));
        $expected[1]->setBody('This is another test which mentions @someone and uses a #hashtag');
        $expected[1]->setParsedBody('This is another test which mentions <a href="https://twitter.com/someone">@someone</a> and uses a <a href="https://twitter.com/hashtag">#hashtag</a>');
        $expected[1]->setURL('https://twitter.com/statuses/848930551989915648');
        $expected[1]->setAuthor('Twitter API');
        $expected[1]->setScreenName('twitterapi');
        $expected[1]->setAuthorURL('https://twitter.com/twitterapi');
        $expected[1]->setAuthorDescription("The Real Twitter API. I tweet about API changes, service issues and happily answer questions about Twitter and our API. Don't get an answer? It's on my website.");
        $expected[1]->setAuthorThumbnail('https://pbs.twimg.com/profile_images/2284174872/7df3h38zabcvjylnyfe3_normal.png');
        $expected[1]->setRetweet($this->getSecondRetweet());

        $this->assertMessage($expected[0], $result[0]);
        $this->assertMessage($expected[1], $result[1]);
    }

    public function testRealFetcherCallsAPIWithCorrectParameters(): void
    {
        $oauthProphecy = $this->getOauthProphecy(self::decodeSampleTwitterMessages());
        $oauthProphecy->get(
            Argument::exact('statuses/user_timeline'),
            Argument::exact(['screen_name' => 'twitterapi', 'count' => 2, 'tweet_mode' => 'extended']))->shouldBeCalledTimes(1);
        $oauthProphecy->getLastBody()->shouldBeCalledTimes(1);

        $fetcher = new TwitterFetcher($oauthProphecy->reveal(), 'twitterapi', 2);
        $fetcher->getData();
    }

    public function testAfterGettingMessageFromGetDataItShowsAPIAsSource(): void
    {
        $oauthProphecy = $this->getOauthProphecy(self::decodeSampleTwitterMessages());

        $fetcher = new TwitterFetcher($oauthProphecy->reveal(), 'twitterapi', 2);
        $results = $fetcher->getData();

        $this->assertSame('API', $results[0]->getFetchSource());
        $this->assertSame('API', $results[1]->getFetchSource());
    }

    public function testAfterGettingMessagesForSecondTimeItShowsCacheAsSource(): void
    {
        $cache = new ArrayAdapter();
        $oauthProphecy1 = $this->getOauthProphecy(self::decodeSampleTwitterMessages());
        $oauthProphecy1->getLastBody()->shouldBeCalledTimes(1);

        $fetcher1 = new TwitterFetcher($oauthProphecy1->reveal(), 'twitterapi', 2);
        $fetcher1->setCache($cache);
        $fetcher1->getData();

        $oauthProphecy2 = $this->getOauthProphecy();
        $oauthProphecy2->getLastBody(Argument::cetera())->shouldNotBeCalled();
        $oauthProphecy2->get(Argument::cetera())->shouldNotBeCalled();

        $fetcher2 = new TwitterFetcher($oauthProphecy2->reveal(), 'twitterapi', 2);
        $fetcher2->setCache($cache);
        $results = $fetcher2->getData();

        $this->assertSame('cache', $results[0]->getFetchSource());
        $this->assertSame('cache', $results[1]->getFetchSource());
    }

    private function getOauthProphecy(array $getLastBodyReturnValue = null): ObjectProphecy
    {
        $prophecy = $this->prophesize(TwitterOAuth::class);
        $prophecy->getLastBody(Argument::cetera())->shouldBeCalled(Argument::cetera());
        $prophecy->get(Argument::cetera())->shouldBeCalled();
        if ($getLastBodyReturnValue !== null) {
            $prophecy->getLastBody(Argument::cetera())->willReturn($getLastBodyReturnValue);
        }

        return $prophecy;
    }

    /**
     * @return Fetcher
     */
    protected function getTestFetcher(): Fetcher
    {
        $oauth = $this->prophesize(TwitterOAuth::class);

        return new TestableTwitterFetcher($oauth->reveal(), 'twitterapi', 2);
    }

    private function getFirstRetweet(): TwitterMessage
    {
        $message = new TwitterMessage();
        $message->setTemplate('twitter.twig');
        $message->setDate(new \DateTime('2017-04-06 15:24:15'));
        $message->setBody('1/ Today we’re sharing our vision for the future of the Twitter API platform!nhttps://t.co/XweGngmxlP');
        $message->setParsedBody('1/ Today we’re sharing our vision for the future of the Twitter API platform!n<a href="https://cards.twitter.com/cards/18ce53wgo4h/3xo1c">cards.twitter.com/cards/18ce53wg…</a>');
        $message->setURL('https://twitter.com/statuses/850006245121695744');
        $message->setAuthor('TwitterDev');
        $message->setScreenName('TwitterDev');
        $message->setAuthorURL('https://twitter.com/TwitterDev');
        $message->setAuthorDescription('Your official source for Twitter Platform news, updates & events. Need technical help? Visit https://t.co/mGHnxZCxkt ⌨️  #TapIntoTwitter');
        $message->setAuthorThumbnail('https://pbs.twimg.com/profile_images/530814764687949824/npQQVkq8_normal.png');

        return $message;
    }

    private function getSecondRetweet(): TwitterMessage
    {
        $message = new TwitterMessage();
        $message->setTemplate('twitter.twig');
        $message->setDate(new \DateTime('2017-04-03 16:05:05'));
        $message->setBody('Starting today, businesses can request and share locations when engaging with people in Direct Messages. https://t.co/rpYndqWfQw');
        $message->setParsedBody('Starting today, businesses can request and share locations when engaging with people in Direct Messages. <a href="https://cards.twitter.com/cards/5wzucr/3x700">cards.twitter.com/cards/5wzucr/3…</a>');
        $message->setURL('https://twitter.com/statuses/848929357519241216');
        $message->setAuthor('Twitter Marketing');
        $message->setScreenName('TwitterMktg');
        $message->setAuthorURL('https://twitter.com/TwitterMktg');
        $message->setAuthorDescription('Twitter’s place for marketers, agencies, and creative thinkers ⭐ Bringing you insights, news, updates, and inspiration. Visit @TwitterAdsHelp for Ads support.');
        $message->setAuthorThumbnail('https://pbs.twimg.com/profile_images/800953549697888256/UlXXL5h5_normal.jpg');

        return $message;
    }
}
