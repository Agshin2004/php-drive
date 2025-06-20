<?php

namespace App\Services;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggerService
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function log(string $msg, array $ctx = [], string $level = LogLevel::INFO)
    {
        $this->logger->log($level, $msg, $ctx);
    }

    public function info(string $msg, array $ctx = []): void
    {
        $this->log($msg, $ctx, LogLevel::INFO);
    }

    public function error(string $msg, array $ctx = []): void
    {
        $this->log($msg, $ctx, LogLevel::ERROR);
    }
}
