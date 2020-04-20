<?php
declare(strict_types=1);

namespace App\ApiService;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerInterface;

class GuzzleService
{
    const REQUEST_FORMAT = "{method} {uri} {req_body}";
    const RESPONSE_FORMAT = "{code} {phrase} {res_body}";

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getClient(): Client
    {
        return new Client([
            'handler' => $this->createHandlerStack(),
        ]);
    }

    private function createHandlerStack(): HandlerStack
    {
        $stack = HandlerStack::create();

        $stack->unshift(
            $this->createLogMiddleware(self::REQUEST_FORMAT)
        );

        $stack->unshift(
            $this->createLogMiddleware(self::RESPONSE_FORMAT)
        );

        return $stack;
    }

    private function createLogMiddleware(string $messageFormat): callable
    {
        return Middleware::log(
            $this->logger,
            new MessageFormatter($messageFormat)
        );
    }
}