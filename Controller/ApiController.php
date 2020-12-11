<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Controller;

use Milosa\SocialMediaAggregatorBundle\Aggregator\SocialMediaAggregator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /**
     * @var SocialMediaAggregator
     */
    private $aggregator;

    public function __construct(SocialMediaAggregator $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    public function getMessages(): Response
    {
        return JsonResponse::fromJsonString($this->convertMessagesToJSON());
    }

    private function convertMessagesToJSON(): string
    {
        return json_encode($this->aggregator->getMessages());
    }
}
