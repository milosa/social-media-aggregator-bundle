<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle;

class Message
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

    public function __construct(string $template = null)
    {
        if ($template !== null) {
            $this->template = $template;
        }
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getURL(): string
    {
        return $this->URL;
    }

    /**
     * @param string $URL
     */
    public function setURL(string $URL): void
    {
        $this->URL = $URL;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
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

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getAuthorURL(): string
    {
        return $this->authorURL;
    }

    /**
     * @return string
     */
    public function getAuthorDescription(): string
    {
        return $this->authorDescription;
    }

    public function setScreenName(string $screenName): void
    {
        $this->screenName = $screenName;
    }

    /**
     * @return string
     */
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
}
