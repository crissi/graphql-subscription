<?php
namespace App\Subscription;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
// not included in this project
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Str;
use GraphQL\Type\Definition\ResolveInfo;

class NewUserSubscription extends Query
{
    protected $attributes = [
        'name' => 'newUser',
    ];

    public function type(): Type
    {
        return GraphQL::type('UserType');
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, $args, $context)
    {
        return [
            'name' => Str::random(10)
        ];
    }
}
