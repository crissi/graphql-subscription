<?php

namespace App\Subscription;

use WebSocket\Client;

class WebSocketBroadcaster
{
    const NOTIFICATION_TYPE = '_notification';

    public function send(string $text)
    {
        $client = new Client('ws://graphql-subscription.me:6001/graphql', [
            'headers' => [
                'Sec-WebSocket-Protocol' => 'graphql-ws'
            ]
        ]);
        $client->send($text);

        return $client->receive();
    }

    public function notify(string $subscription, array $context = [])
    {
        $message = [
            'type' => self::NOTIFICATION_TYPE,
            'name' => $subscription,
            'context' => $context
        ];
        logger($message);
        $this->send(json_encode($message));
    }

    // public function sendGraphqlMessage(string $query, array $variables)
    // {
    //     $message = [
    //         'type' => 'start',
    //         'payload' => [
    //             'variables' => $variables,
    //             'query' => $query,
    //             'operationName' => null
    //         ],
    //         'id' => 1
    //     ];
    //     logger($message);
    //     $this->send(json_encode($message));
    //}
}
