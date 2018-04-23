<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle;

use Twig_Environment;

class Renderer
{
    /**
     * @var Twig_Environment
     */
    private static $twig;

    /**
     * Renders an array of Messages, turning all messages into a rendered string.
     *
     * @param Message[] $messages
     *
     * @return string
     */
    public static function renderArrayToString(array $messages): string
    {
        $return = '';

        foreach ($messages as $message) {
            self::checkTemplate($message);
            $return .= self::renderMessage($message);
            $return .= "\n";
        }

        return $return;
    }

    /**
     * Renders an array of Messages, returning an array of Message objects with their body field rendered.
     *
     * @param Message[] $messages
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Message[]
     */
    public static function renderArray(array $messages): array
    {
        $return = [];

        /**
         * @var Message $message
         */
        foreach ($messages as $message) {
            self::checkTemplate($message);
            $newBody = self::renderMessage($message);
            $message->setBody($newBody);
            $return[] = $message;
        }

        return $return;
    }

    /**
     * Renders an individual Message Object into a string.
     *
     * @param Message $message
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return string
     */
    public static function renderMessage(Message $message): string
    {
        self::checkTwig();
        self::checkTemplate($message);

        return self::$twig->render('@MilosaSocialMediaAggregatorBundle/'.$message->getTemplate(),
            [
                'message' => $message,
            ]);
    }

    public static function initialize(Twig_Environment $twig): void
    {
        self::$twig = $twig;
    }

    public static function renderMessagesNew(array $messages): string
    {
        return self::$twig->render('@MilosaSocialMediaAggregatorBundle/feed.twig',
            [
                'messages' => $messages,
            ]);
    }

    private static function checkTwig(): void
    {
        if (self::$twig === null) {
            throw new \RuntimeException('Twig not loaded in Renderer');
        }
    }

    /**
     * @param Message $message
     *
     * @throws \RuntimeException
     */
    private static function checkTemplate(Message $message): void
    {
        if ($message->getTemplate() === null) {
            throw new \RuntimeException('Template file not found');
        }
    }
}
