<?php

namespace App\Subscription;

use Ratchet\ConnectionInterface;
use InvalidArgumentException;

class ConnectionDataStore
{
    private $queries;

    public function __construct()
    {
        $this->queries = [];
    }

    public function addQuery(ConnectionQuery $query)
    {
        $queryName = $this->getQueryName($query->query());
        $this->queries[$queryName][] = $query;
    }

    public function getConnectionsForQuery(string $queryName): array
    {
        if (isset($this->queries[$queryName])) {
            return $this->queries[$queryName];
        }
        return [];
    }

    private function getQueryName(string $query): string
    {
        logger($query);
        preg_match('/subscription?\s*\w+?\s*{\s+(\w+)/', $query, $matches);
        if (!isset($matches[1])) {
            throw new InvalidArgumentException('Not a valid subscription query');
        }
        return $matches[1];
    }

    public function detach(ConnectionInterface $connection): void
    {
        unset($this->clients[$connection]);
    }
}
