<?php

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

// Create router instance
$router = Router::getRouter();

// Register the routes
$router->get('/', function ()
{
    echo <<<HTML
    <h1>Welcome to Job Dating App</h1>
    <p>Test routes:</p>
    <ul>
        <li><a href="/user/5/product/laptop">Test User 5 - Laptop</a></li>
        <li><a href="/user/9/product/mouse">Test User 9 - Mouse</a></li>
    </ul>
    HTML;
});

$router->get('/user/{user:[1-9]+}/product/{product}', function ($user, $product)
{
    echo <<<HTML
    User ID : $user <br> Product Slug : $product
    HTML;
});

// Dispatch the router
$router->dispatch();