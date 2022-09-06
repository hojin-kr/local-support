<?php

namespace Hojin\Url\Logger;

use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;
use Google\Cloud\Logging\LoggingClient;

class Logger
{
    const ENV_CLOUD = "CLOUD";

    private $ENV = "local";
    private $logger;

    public function __construct()
    {
        $this->ENV = getenv("ENV");
        // config
        if (static::ENV_CLOUD == $this->ENV) {
            $logging = new LoggingClient([
                'projectId' => getenv("PROJECT_ID")
            ]);
            $this->logger = $logging->psrLogger('tldr');
        } else {
            $logLevel = MonoLogger::DEBUG;
            $isStream = false;
            $this->logger = new MonoLogger('log');
            $stream = "log/".Date("Ymd").".log";
            if ($isStream) {
                $stream = fopen("php://stdout", "w");
            }
            $this->logger->pushHandler(new StreamHandler($stream, $logLevel));
        }
    }

    public function info(string $message, array $context) {
        $this->logger->info($message, $context);
    }

    public function error(string $message, array $context) {
        $this->logger->error($message, $context);
    }

}