<?php

namespace App\Subscription;

use Ratchet\ConnectionInterface;

class ConnectionQuery
{
    private $connection;
    private $query;
    private $variables;
    private $instanceId;

    public function __construct(ConnectionInterface $connection, string $query, array $variables, ?int $instanceId)
    {
        $this->connection = $connection;
        $this->query = $query;
        $this->variables = $variables;
        $this->instanceId = $instanceId;
    }

    public function connection(): ConnectionInterface
    {
        return $this->connection;
    }

    public function query(): string
    {
        return $this->query;
    }

    public function variables(): array
    {
        return $this->variables;
    }

    /**
     * GraphQL playground is sending an id-field in its
     * requests and need to receive the same id on notifications.
     * I assume it identifies the instance/tab
    */
    public function instanceId(): ?int
    {
        return $this->instanceId;
    }
}
