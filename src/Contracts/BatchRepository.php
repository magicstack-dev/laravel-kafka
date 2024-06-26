<?php

declare(strict_types=1);

namespace Junges\Kafka\Contracts;

use Illuminate\Support\Collection;
use RdKafka\Message;

interface BatchRepository
{
    /** Pushes new message to batch repository */
    public function push(Message $message): void;

    /** Returns all messages from batch repository */
    public function getBatch(): Collection;

    /** Returns current size of a batch */
    public function getBatchSize(): int;

    /** Deletes all messages from batch repository */
    public function reset(): void;
}
