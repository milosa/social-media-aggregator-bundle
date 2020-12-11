<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Tests;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testMessageReturnsRightValues(): void
    {
        $message = new Message('test');
        $message->setBody('test body');
        $message->setDate(new \DateTime('1947-07-08'));
        $message->setTemplate('test template');
        $message->setURL('google.com');
        $message->setAuthor('John doe');
        $message->setAuthorURL('johndoe.com');
        $message->setAuthorDescription('This is John Doe!');
        $message->setAuthorThumbnail('https://johndoe.com/johnny.jpg');
        $message->setScreenName('johnny_2018');
        $message->setTitle('test_title');
        $message->setId('test_id');
        $message->setParsedBody('test body');
        $message->setNetwork('TEST');

        $this->assertSame('test body', $message->getBody());
        $this->assertEquals(new \DateTime('1947-07-08'), $message->getDate());
        $this->assertSame('test template', $message->getTemplate());
        $this->assertSame('google.com', $message->getURL());
        $this->assertSame('John doe', $message->getAuthor());
        $this->assertSame('johndoe.com', $message->getAuthorURL());
        $this->assertSame('This is John Doe!', $message->getAuthorDescription());
        $this->assertSame('https://johndoe.com/johnny.jpg', $message->getAuthorThumbnail());
        $this->assertSame('johnny_2018', $message->getScreenName());
        $this->assertSame('test', $message->getFetchSource());
        $this->assertSame('test_title', $message->getTitle());
        $this->assertSame('test_id', $message->getId());
        $this->assertSame('test body', $message->getParsedBody());
        $this->assertSame('TEST', $message->getNetwork());
    }

    public function testMessageDefaultsToNoSource(): void
    {
        $message = new Message();

        $this->assertNull($message->getFetchSource());
    }

    public function testMessageTemplateCanBeSetFromConstructor(): void
    {
        $message = new Message(null, 'test.twig');

        $this->assertSame('test.twig', $message->getTemplate());
    }
}
