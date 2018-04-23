<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites;

use Abraham\TwitterOAuth\TwitterOAuth;
use Milosa\SocialMediaAggregatorBundle\Message;
use Milosa\SocialMediaAggregatorBundle\Sites\Fetcher;
use Milosa\SocialMediaAggregatorBundle\Sites\TwitterFetcher;
use Prophecy\Argument;

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

    public function testGetMessageObjects(): void
    {
        $result = $this->fetcher->getData();

        $this->assertNotNull($result);
        $this->assertCount(2, $result);

        $this->assertInstanceOf(Message::class, $result[0]);
        $this->assertInstanceOf(Message::class, $result[1]);

        $expected = [];
        $expected[0] = new Message();
        $expected[0]->setDate(new \DateTime('2018-01-18 18:47:36'));
        $expected[0]->setTemplate('twitter.twig');
        $expected[0]->setBody('Dit is een test text');
        $expected[0]->setURL('https://twitter.com/statuses/850007368138018817');
        $expected[0]->setAuthor('Twitter API');
        $expected[0]->setScreenName('twitterapi');
        $expected[0]->setAuthorURL('https://twitter.com/twitterapi');
        $expected[0]->setAuthorDescription("The Real Twitter API. I tweet about API changes, service issues and happily answer questions about Twitter and our API. Don't get an answer? It's on my website.");
        $expected[0]->setAuthorThumbnail('https://pbs.twimg.com/profile_images/2284174872/7df3h38zabcvjylnyfe3_normal.png');

        $expected[1] = new Message();
        $expected[1]->setTemplate('twitter.twig');
        $expected[1]->setDate(new \DateTime('2018-01-18 07:38:10'));
        $expected[1]->setBody('Een test text dit is');
        $expected[1]->setURL('https://twitter.com/statuses/848930551989915648');
        $expected[1]->setAuthor('Twitter API');
        $expected[1]->setScreenName('twitterapi');
        $expected[1]->setAuthorURL('https://twitter.com/twitterapi');
        $expected[1]->setAuthorDescription("The Real Twitter API. I tweet about API changes, service issues and happily answer questions about Twitter and our API. Don't get an answer? It's on my website.");
        $expected[1]->setAuthorThumbnail('https://pbs.twimg.com/profile_images/2284174872/7df3h38zabcvjylnyfe3_normal.png');

        $this->assertMessage($expected[0], $result[0]);
        $this->assertMessage($expected[1], $result[1]);
    }

    public function testRealFetcherCallsAPIWithCorrectParameters(): void
    {
        $oauthProphecy = $this->prophesize(TwitterOAuth::class);
        $oauthProphecy->get(
            Argument::exact('statuses/user_timeline'),
            Argument::exact(['screen_name' => 'twitterapi', 'count' => 2]))->shouldBeCalledTimes(1);
        $oauthProphecy->getLastBody()->shouldBeCalledTimes(1)->willReturn(self::getDecodedTwitterJson());

        $fetcher = new TwitterFetcher($oauthProphecy->reveal(), 'twitterapi', 2);
        $fetcher->getData();
    }

    /**
     * @return Fetcher
     */
    protected function getTestFetcher(): Fetcher
    {
        $oauth = $this->prophesize(TwitterOAuth::class);

        return new TestableTwitterFetcher($oauth->reveal(), 'twitterapi', 2);
    }
}
