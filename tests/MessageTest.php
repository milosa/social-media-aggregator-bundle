<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests;

use Milosa\SocialMediaAggregator\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testSomething()
    {
        $this->assertTrue(true);
    }

    public function testMessageReturnsRightValues(): void
    {
        $message = new Message();
        $message->setBody('test body');
        $message->setDate(new \DateTime('1947-07-08'));
        $message->setTemplate('test template');
        $message->setURL('google.com');

        $this->assertSame('test body', $message->getBody());
        $this->assertEquals(new \DateTime('1947-07-08'), $message->getDate());
        $this->assertSame('test template', $message->getTemplate());
        $this->assertSame('google.com', $message->getURL());
    }
}
