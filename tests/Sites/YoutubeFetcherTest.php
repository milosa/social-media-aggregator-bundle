<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Milosa\SocialMediaAggregatorBundle\Message;
use Milosa\SocialMediaAggregatorBundle\Sites\Fetcher;
use Milosa\SocialMediaAggregatorBundle\Sites\YoutubeFetcher;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class YoutubeFetcherTest extends FetcherTestCase
{
    use TestDataTrait;

    private const YOUTUBE_TEST_API_URL = 'https://www.googleapis.com/youtube/v3/search?part=snippet,id&channelId=UCLA_DiR1FfKNvjuUpBHmylQ&maxResults=2&order=date&type=video&key=test_key';

    /**
     * @var Fetcher
     */
    protected $fetcher;

    public function setUp()
    {
        $this->fetcher = $this->getTestFetcher();
    }

    protected function getTestFetcher(): Fetcher
    {
        $client = $this->prophesize(Client::class);

        return new TestableYoutubeFetcher($client->reveal());
    }

    public function testGetMessagesObjects(): void
    {
        $result = $this->fetcher->getData();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Message::class, $result[0]);
        $this->assertInstanceOf(Message::class, $result[1]);

        $expected = [];
        $expected[0] = new Message();
        $expected[0]->setDate(new \DateTime('2018-04-27 19:36:41'));
        $expected[0]->setTemplate('youtube.twig');
        $expected[0]->setBody('Vice President Pence swears in our new NASA Administrator, a Hubble anniversary flythrough of a nebula, and the smell in the clouds of one of our outermost planets – a few of the stories...');
        $expected[0]->setURL('https://www.youtube.com/watch?v=km9T0IMjr98');
        $expected[0]->setAuthor('NASA');
        $expected[0]->setTitle('Bridenstine Sworn in as NASA Administrator on This Week @NASA – April 27, 2018');
        $expected[0]->setAuthorURL('https://www.youtube.com/channel/UCLA_DiR1FfKNvjuUpBHmylQ');
        $expected[0]->setId('km9T0IMjr98');

        $expected[1] = new Message();
        $expected[1]->setDate(new \DateTime('2018-04-24 15:27:49'));
        $expected[1]->setTemplate('youtube.twig');
        $expected[1]->setBody('Jim Bridenstine officially took office as the 13th administrator of NASA on Monday, April 23rd, after he was given the oath of office by Vice President Mike Pence at the agency\'s headquarters...');
        $expected[1]->setURL('https://www.youtube.com/watch?v=DwaD0UKsX3g');
        $expected[1]->setTitle('Welcome Jim Bridenstine to the NASA Family');
        $expected[1]->setAuthor('NASA');
        $expected[1]->setAuthorURL('https://www.youtube.com/channel/UCLA_DiR1FfKNvjuUpBHmylQ');
        $expected[1]->setId('DwaD0UKsX3g');

        $this->assertMessage($expected[0], $result[0]);
        $this->assertMessage($expected[1], $result[1]);
    }

    public function testRealFetcherCallsAPIwithCorrectURL(): void
    {
        $clientProphecy = $this->clientProphecyFactory(self::YOUTUBE_TEST_API_URL, self::jsonYoutubeSampleData());

        $fetcher = new YoutubeFetcher($clientProphecy->reveal(), 'UCLA_DiR1FfKNvjuUpBHmylQ', 2, 'test_key');
        $fetcher->getData();
    }

    public function testAfterGettingMessagesFromGetDataItShowsAPIAsSource(): void
    {
        $clientProphecy = $this->clientProphecyFactory(self::YOUTUBE_TEST_API_URL, self::jsonYoutubeSampleData());

        $fetcher = new YoutubeFetcher($clientProphecy->reveal(), 'UCLA_DiR1FfKNvjuUpBHmylQ', 2, 'test_key');
        $results = $fetcher->getData();

        $this->assertSame('API', $results[0]->getFetchSource());
        $this->assertSame('API', $results[1]->getFetchSource());
    }

    private function clientProphecyFactory(string $expectedURL = null, string $returnContent = null, bool $noCalls = false)
    {
        $streamInterfaceProphecy = $this->getStreamInterfaceProphecy();
        $responseProphecy = $this->getResponseProphecy();
        if ($returnContent !== null) {
            $streamInterfaceProphecy
                ->getContents()
                ->shouldBeCalled(1)
                ->willReturn($returnContent);

            $responseProphecy
                ->getBody()
                ->shouldBeCalled(1)
                ->willReturn($streamInterfaceProphecy->reveal());
        } elseif ($noCalls === true) {
            $streamInterfaceProphecy->getContents()->shouldNotBeCalled();
            $responseProphecy->getBody()->shouldNotBeCalled();
        }

        $clientProphecy = $this->getClientProphecy();

        if ($expectedURL !== null) {
            $clientProphecy
                ->request(
                    Argument::exact('GET'),
                    Argument::exact($expectedURL))
                ->shouldBeCalled(1)
                ->willReturn($responseProphecy->reveal());
        }

        return $clientProphecy;
    }

    private function getClientProphecy(): ObjectProphecy
    {
        return $this->prophesize(Client::class);
    }

    public function testAfterGettingMessagesForSecondTimeItShowsCacheAsSource(): void
    {
        $cache = new ArrayAdapter();
        $clientProphecy1 = $this->clientProphecyFactory(self::YOUTUBE_TEST_API_URL, self::jsonYoutubeSampleData());

        $fetcher1 = new YoutubeFetcher($clientProphecy1->reveal(), 'UCLA_DiR1FfKNvjuUpBHmylQ', 2, 'test_key');
        $fetcher1->setCache($cache);
        $fetcher1->getData();

        $clientProphecy2 = $this->clientProphecyFactory(null, null, true);

        $fetcher2 = new YoutubeFetcher($clientProphecy2->reveal(), 'UCLA_DiR1FfKNvjuUpBHmylQ', 2, 'test_key');
        $fetcher2->setCache($cache);
        $results = $fetcher2->getData();

        $this->assertSame('cache', $results[0]->getFetchSource());
        $this->assertSame('cache', $results[1]->getFetchSource());
    }

    public function testCanCreateFetcher(): void
    {
        $this->assertInstanceOf(YoutubeFetcher::class, $this->fetcher);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->fetcher = null;
    }

    /**
     * @return ObjectProphecy
     */
    private function getStreamInterfaceProphecy(): ObjectProphecy
    {
        $streamInterfaceProphecy = $this->prophesize(StreamInterface::class);

        return $streamInterfaceProphecy;
    }

    /**
     * @return ObjectProphecy
     */
    private function getResponseProphecy(): ObjectProphecy
    {
        $responseProphecy = $this->prophesize(Response::class);

        return $responseProphecy;
    }
}
