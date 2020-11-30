<?php

namespace App\Subscription;

use Rebing\GraphQL\Support\Query;
use App\User;
use App\WebSocketBroadcaster;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GraphQL\Type\Definition\Type;
use Closure;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UserQuery extends Query
{

    protected $attributes = [
        'name' => 'user',
    ];

    public function type(): Type
    {
        return GraphQL::type('UserType');;
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'rules' => ['integer', 'max:2']
            ]
        ];
    }

    public function resolve($root, array $args, $context, Closure $getSelectFields)
    {
        $fields = $getSelectFields();
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        return User::where('id', 1)
            ->select($select)
            ->with($with)
            ->first();
    }
}
