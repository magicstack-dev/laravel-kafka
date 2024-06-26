<?php

declare(strict_types=1);

namespace Junges\Kafka;

use Illuminate\Support\Traits\Macroable;
use Junges\Kafka\Consumers\Builder as ConsumerBuilder;
use Junges\Kafka\Contracts\ConsumerMessage;
use Junges\Kafka\Contracts\Manager;
use Junges\Kafka\Contracts\MessageProducer;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Producers\Builder as ProducerBuilder;

class Factory implements Manager
{
    use Macroable;

    private bool $shouldFake = false;

    /** @var array<int, ConsumerMessage> This array is passed to the underlying consumer when faking macroed consumers. */
    private array $fakeMessages = [];

    /** Creates a new ProducerBuilder instance, setting brokers and topic. */
    public function publish(?string $broker = null): MessageProducer
    {
        if ($this->shouldFake) {
            return Kafka::fake()->publish($broker);
        }

        return new ProducerBuilder(
            broker: $broker ?? config('kafka.brokers')
        );
    }

    /** Return a ConsumerBuilder instance.  */
    public function consumer(array $topics = [], ?string $groupId = null, ?string $brokers = null): ConsumerBuilder
    {
        if ($this->shouldFake) {
            return Kafka::fake()->consumer(
                $topics,
                $groupId,
                $brokers
            )->setMessages($this->fakeMessages);
        }

        return ConsumerBuilder::create(
            brokers: $brokers ?? config('kafka.brokers'),
            topics: $topics,
            groupId: $groupId ?? config('kafka.consumer_group_id')
        );
    }

    public function shouldFake(): self
    {
        $this->shouldFake = true;

        return $this;
    }

    /** @param array<int, ConsumerMessage> $messages */
    public function shouldReceiveMessages(array $messages): self
    {
        $this->fakeMessages = $messages;

        return $this;
    }
}
