<?php

declare(strict_types=1);

namespace App\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    /**
     * @var string
     */
    private string $path;
    /**
     * @var int
     */
    private int $level;
    /**
     * @var array Handler
     */
    private array $handler = [];

    /**
     * * @var LoggerInterface|null
     */
    private ?string $uuid;

    public function __construct(array $loggerSettings)
    {
        $this->path = $loggerSettings['path'];
        $this->level = $loggerSettings['level'];
        if (!isset($this->uuid)) {
            $this->uuid = uuid_create();
        }
    }

    public function getUUID(): ?string
    {
        return $this->uuid;
    }

    /**
     * Build the logger.
     *
     * @param string|null $name The logging channel
     *
     * @return LoggerInterface The logger
     */
    public function createLogger(string $name = null): LoggerInterface
    {
//        if ($this->testLogger) {
//            return $this->testLogger;
//        }

        $logger = new Logger($name ?: $this->uuid);
        foreach ($this->handler as $handler) {
            $logger->pushHandler($handler);
        }
        $this->handler = [];
        return $logger;
    }


    /**
     * Add rotating file logger handler.
     *
     * @param string $filename The filename
     * @param int|null $level The level (optional)
     *
     * @return self The logger factory
     */
    public function addFileHandler(string $filename, int $level = null, $filePermission = null): self
    {
        #TODO Setup logrotate at the os level
        $filename = sprintf('%s/%s', $this->path, $filename);
        $rotatingFileHandler = new RotatingFileHandler($filename, 0, $level ?? $this->level, true, 0775);
        // The last "true" here tells monolog to remove empty []'s
        $rotatingFileHandler->setFormatter(new LineFormatter(null, "n-j-Y, g:i:s A", false, true));
        $this->handler[] = $rotatingFileHandler;
        return $this;
    }


    /**
     * Add a console logger.
     *
     * @param int|null $level The level (optional)
     *
     * @return self The logger factory
     */
    public function addConsoleHandler(int $level = null): self
    {
        $streamHandler = new StreamHandler('php://stdout', $level ?? $this->level);
        $streamHandler->setFormatter(new LineFormatter(null, "n-j-Y, g:i:s A", false, true));
        $this->handler[] = $streamHandler;
        return $this;
    }


}