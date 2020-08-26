<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator;

use JsonSerializable;

class Message implements JsonSerializable
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $URL;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $template;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $authorURL;

    /**
     * @var string
     */
    private $authorDescription;

    /**
     * @var string
     */
    private $screenName;

    /**
     * @var string
     */
    private $authorThumbnail;

    /**
     * @var ?string
     */
    private $fetchSource;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $parsedBody;

    /**
     * @var string
     */
    private $network;

    public function __construct(string $fetchSource = null, string $template = null)
    {
        if ($template !== null) {
            $this->template = $template;
        }

        $this->fetchSource = $fetchSource;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getURL(): string
    {
        return $this->URL;
    }

    public function setURL(string $URL): void
    {
        $this->URL = $URL;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function setAuthorURL(string $authorURL): void
    {
        $this->authorURL = $authorURL;
    }

    public function setAuthorDescription(string $authorDescription): void
    {
        $this->authorDescription = $authorDescription;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getAuthorURL(): string
    {
        return $this->authorURL;
    }

    public function getAuthorDescription(): string
    {
        return $this->authorDescription;
    }

    public function setScreenName(string $screenName): void
    {
        $this->screenName = $screenName;
    }

    public function getScreenName(): string
    {
        return $this->screenName;
    }

    public function setAuthorThumbnail(string $thumbnailURL): void
    {
        $this->authorThumbnail = $thumbnailURL;
    }

    public function getAuthorThumbnail(): string
    {
        return $this->authorThumbnail;
    }

    public function getFetchSource(): ?string
    {
        return $this->fetchSource;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setParsedBody(string $body): void
    {
        $this->parsedBody = $body;
    }

    public function getParsedBody(): string
    {
        return $this->parsedBody;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function setNetwork(string $networkName): void
    {
        $this->network = $networkName;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }
}
