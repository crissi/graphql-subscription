<?php

namespace App\Subscription;

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\WebSocket\WsServerInterface;
use graphql;
use App\Subscription\ConnectionDataStore;
use App\Subscription\ConnectionQuery;
use Illuminate\Support\Facades\Cookie;
use Dflydev\FigCookies\Cookies;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Exception;

class GraphQLWebSocketHandler implements MessageComponentInterface, WsServerInterface
{
    private $connectionData;

    public function __construct()
    {
        $this->connectionData = new ConnectionDataStore;
    }

    public function onOpen(ConnectionInterface $connection)
    {
        $connection->send(json_encode([
              'type' => 'connection_ack',
        ]));
    }

    public function onClose(ConnectionInterface $connection)
    {
        $this->connectionData->detach($connection);
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        logger($e->getMessage());
        $connection->close();
    }

    protected function queryContext()
    {
        try {
            return app('auth')->user();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function startSession(ConnectionInterface $connection)
    {
        $cookiename = Config::get('session.cookie');
        $cookies = Cookies::fromRequest($connection->httpRequest);

        if (!$cookies->has($cookiename)) {
            return;
        }

        $value = Crypt::decryptString($cookies->get($cookiename)->getValue());

        $sessionDriver = session()->driver();
        $sessionDriver->setId($value);
        $sessionDriver->start();
    }

    public function onMessage(ConnectionInterface $connection, MessageInterface $msg)
    {
        $this->startSession($connection);

        $data = json_decode($msg->getPayload(), true);

        if ($this->isNotification($data)) {
            $queryConnections = $this->connectionData->getConnectionsForQuery($data['name']);
            if (count($queryConnections) === 0) {
                return;
            }

            foreach ($queryConnections as $query) {
                $message = $this->createGraphqlResponse($query->query(), $query->variables(), $query->instanceId());

                $query->connection()->send(json_encode($message));
            }

        } elseif ($this->isAGraphQLSubscribeRequest($data)) {
            $payload = $data['payload'];

            $this->connectionData->addQuery(
                new ConnectionQuery($connection, $payload['query'], $payload['variables'], $data['id'] ?? null)
            );
        }
    }

    public function isAGraphQLSubscribeRequest(array $data): bool
    {
        return isset($data['type']) && $data['type'] === 'start';
    }

    public function isNotification(array $data): bool
    {
        return isset($data['type']) && $data['type'] === WebSocketBroadcaster::NOTIFICATION_TYPE;
    }

    private function createGraphqlResponse(string $query, array $variables, ?int $id, array $context = []): array
    {
        $opts = [
            'context' => $this->queryContext(),
            'schema' => config('graphql.default_schema'),
        ];

        $res = app('graphql')->query($query, $variables, array_merge($opts, [
                'operationName' => $payload['operationName'] ?? null,
                'context' => $context
        ]));

        return [
            'type' => 'data',
            'payload' => $res,
            'id' => $id
        ];
    }

    public function getSubProtocols()
    {
        return ['graphql-ws'];
    }
}
