<?php

use App\Controllers\MainController;
use App\Controllers\AuthController;
use App\Controllers\ConversationsController;

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;


//    Routes where authentication does not matter should be placed inside this group
$app->get('[/]', MainController::class . ':home')->setName('home');

$app->group('', function() use ($app) {
    $app->get('/conversations[/]', ConversationsController::class . ':getMyConversations')->setName('conversations.myConversations');
    $app->post('/conversations/create[/]', ConversationsController::class . ':createConversation')->setName('conversations.create');
    $app->map(['GET', 'POST'], '/conversations/show[/]', ConversationsController::class . ':showConversation')->setName('conversations.show');
    $app->post('/conversations/ajax/generate-links[/]', ConversationsController::class . ':generateConversationLinks')->setName('conversations.generateLinks');
    $app->post('/conversations/ajax/send-message[/]', ConversationsController::class . ':sendMessage')->setName('conversations.sendMessage');
    
})->add(new AuthMiddleware($container));

$app->group('/auth', function() use ($app) {
    $app->get('/login[/]', AuthController::class . ':login')->setName('auth.login');
    $app->post('/login[/]', AuthController::class . ':postLogin')->setName('auth.login.post');
    $app->get('/signup[/]', AuthController::class . ':signup')->setName('auth.signup');
    $app->post('/signup[/]', AuthController::class . ':postSignup')->setName('auth.signup.post');
})->add(new GuestMiddleware($container));