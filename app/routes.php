<?php

// Home page
$app->get('/', "WebLinks\Controller\HomeController::indexAction")->bind('home');

// login form
$app->get('/login', "WebLinks\Controller\HomeController::loginAction")->bind('login');

// Admin Page
$app->get('/admin', "WebLinks\Controller\AdminController::indexAction")->bind('admin');

//link edit 
$app->match('/admin/link/{id}/edit', "WebLinks\Controller\AdminController::linkEditAction")->bind('admin_link_edit');

//link delete 
$app->get('/admin/link/{id}/delete/', "WebLinks\Controller\AdminController::linkDeleteAction")->bind('admin_link_delete');

//user edit 
$app->match('/admin/user/{id}/edit', "WebLinks\Controller\AdminController::userEditAction")->bind('admin_user_edit');

//user delete 
$app->get('/admin/user/{id}/delete', "WebLinks\Controller\AdminController::userDeleteAction")->bind('admin_user_delete');

// create link form
$app->match('/link/submit', "WebLinks\Controller\HomeController::linkCreateAction")->bind('createLink');

// Api links list
$app->get('/api/links', "WebLinks\Controller\ApiController::linksAction")->bind('api_links');

// Api link infos
$app->get('/api/link/{id}', "WebLinks\Controller\ApiController::linkInfosAction")->bind('api_link_info');
