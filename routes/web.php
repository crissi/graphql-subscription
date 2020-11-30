<?php

use Illuminate\Support\Facades\Route;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use BeyondCode\LaravelWebSockets\Server\Logger\WebsocketsLogger;
use Symfony\Component\Console\Output\NullOutput;
use App\Subscription\WebSocketBroadcaster;
use Illuminate\Support\Str;
use App\Subscription\GraphQLWebSocketHandler;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

app()->singleton(WebsocketsLogger::class, function () {
    return (new WebsocketsLogger(new NullOutput()))->enable(false);
});

//WebSocketsRouter::webSocket('/graphql', MyCustomWebSocketHandler::class);
WebSocketsRouter::get('/graphql', GraphQLWebSocketHandler::class);

Route::get('/broadcast', function () {
    (new WebSocketBroadcaster)->notify('newUser', [
        'name' => Str::random('10')
    ]);
});
