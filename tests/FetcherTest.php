<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\tests;

use Milosa\SocialMediaAggregatorBundle\Aggregator\ClientWrapper;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Fetcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class FetcherTest extends TestCase
{
    public function testSetCache(): void
    {
        $fetcher = new TestFetcher($this->prophesize(ClientWrapper::class)->reveal());
        $cache = $this->createMock(AdapterInterface::class);
        $fetcher->setCache($cache);

        $this->assertEquals($cache, $fetcher->getCache());
    }

    public function testInjectSource(): void
    {
        $fetcher = new TestFetcher($this->prophesize(ClientWrapper::class)->reveal());
        $messages = [new \stdClass(), new \stdClass()];
        $result = $fetcher->testableInjectSource($messages, 'test_source');

        $expectedObject = new \stdClass();
        $expectedObject->fetchSource = 'test_source';

        $this->assertEquals([
            $expectedObject,
            $expectedObject,
            ], $result);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Required setting 'test_setting' is missing
     */
    public function testOmittingOneRequiredSettingThrowsException(): void
    {
        $fetcher = new ConstructorArgumentsTestFetcher($this->prophesize(ClientWrapper::class)->reveal());
        $fetcher->addRequiredSetting('test_setting');

        $fetcher->testValidateSettings();
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Required settings 'test_setting, test_setting2' are missing
     */
    public function testOmittingMultipleRequiredSettingThrowsException(): void
    {
        $fetcher = new ConstructorArgumentsTestFetcher($this->prophesize(ClientWrapper::class)->reveal());
        $fetcher->addRequiredSetting('test_setting');
        $fetcher->addRequiredSetting('test_setting2');

        $fetcher->testValidateSettings();
    }

    public function testAddingSettings(): void
    {
        $fetcher = new ConstructorArgumentsTestFetcher($this->prophesize(ClientWrapper::class)->reveal(), ['number_of_messages' => 2]);

        $this->assertEquals(2, $fetcher->getSetting('number_of_messages'));
    }

    public function testAddingRequiredSettings(): void
    {
        $fetcher = new ConstructorArgumentsTestFetcher($this->prophesize(ClientWrapper::class)->reveal(), ['number_of_messages' => 2]);
        $fetcher->addRequiredSetting('number_of_messages');

        $this->assertEquals(2, $fetcher->getSetting('number_of_messages'));
    }
}

class TestFetcher extends Fetcher
{
    public function fetch(): array
    {
        // TODO: Implement fetch() method.
    }

    public function getCache(): AdapterInterface
    {
        return $this->cache;
    }

    public function testableInjectSource(array $messages, string $source): array
    {
        return $this->injectSource($messages, $source);
    }
}

class ConstructorArgumentsTestFetcher extends Fetcher
{
    public function fetch(): array
    {
    }

    public function testValidateAuthData()
    {
        $this->validateAuthData();
    }

    public function testValidateSettings(): void
    {
        $this->validateConfig();
    }

    public function getSetting(string $name)
    {
        return $this->config[$name];
    }

    public function addRequiredSetting(string $name): void
    {
        $this->requiredSettings[] = $name;
    }

    public function addRequiredAuth(string $name): void
    {
        $this->requiredAuthData[] = $name;
    }

    public function getAuthData(string $name)
    {
        return $this->authData[$name];
    }
}
