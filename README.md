
## About
Proof of concept to get subscription working with https://github.com/rebing/graphql-laravel

## Setup
To use graphql-playground (not possible to set the subscription url) rewriting url to port 6001 is needed, This is how i done with Apache

    <VirtualHost *:80> 
        ...

        RewriteEngine On
        RewriteCond %{HTTP:Upgrade} =websocket [NC]
        RewriteRule /(.*)           ws://127.0.0.1:6001/$1 [P,L]

    </VirtualHost>

Todo: add docker or nginx example

## Get going

1. Start a web server
    ...

2. Start the websocket server

   **artisan websockets:serve**

3. Test in playground
   1. Go to: https://localhost/graphql-playground
   
   2. Input the the newUser subscription
   3. Click run
   4. Fire an broadcast call with https://localhost/broadcast
   5. See new data showing up in the interface
4. Or with Angular:
    https://github.com/crissi/angular-subscriptions

## Todo
Add docker or nginx example
