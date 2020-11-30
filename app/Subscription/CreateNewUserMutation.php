<?php
namespace App\Subscription;

use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use App\WebSocketBroadcaster;

class CreateNewUserMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createNewUser'
    ];

    public function type(): Type
    {
        return GraphQL::type('UserType');
    }

    public function args(): array
    {
        return [
            'name' => [
                'type' => Type::string()
            ],
        ];
    }

    public function resolve($root, $args)
    {
        //save user
        (new WebSocketBroadcaster)->notify('newUser', [
            'name' => Str::random('10')
        ]);
        //notify websocket about new user
        return [
            'name' => array_get($args, 'name') ?? '___'
        ];
    }
}
