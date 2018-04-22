<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator;

use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

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

        return self::$twig->render('@MilosaSocialMediaAggregator/'.$message->getTemplate(),
            [
                'message' => $message,
            ]);
    }

    public static function initialize(Twig_Environment $twig): void
    {
//        if (self::$twig === null) {
//            self::$twig = new Twig_Environment(new Twig_Loader_Filesystem(__DIR__ . '/Resources/views'), ['debug' => true]);
//
//            self::$twig->addExtension(new Twig_Extension_Debug());
//        }
        self::$twig = $twig;
    }

    private static function checkTwig(): void
    {
        if (self::$twig === null) {
            throw new \RuntimeException('Twig not loaded in Renderer');
        }
    }

    public static function renderMessagesNew(array $messages): string
    {
        return self::$twig->render('@MilosaSocialMediaAggregator/feed.twig',
            [
                'messages' => $messages,
            ]);
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
