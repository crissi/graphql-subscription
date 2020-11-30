
## About


## Setup


#Rewrite url to
<VirtualHost *:80> 
    ...



    RewriteEngine On
    RewriteCond %{HTTP:Upgrade} =websocket [NC]
    RewriteRule /(.*)           ws://127.0.0.1:6001/$1 [P,L]

</VirtualHost>

## Get going

Start the web server
docker-compose up

Start websocket server
docker-compose exec -T web php artisan websockets:serve


