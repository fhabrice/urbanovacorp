<?php

return [
    // Home page
    ['method' => 'GET', 'path' => '/', 'handler' => 'HomeController@index'],

    // About pages
    ['method' => 'GET', 'path' => '/about', 'handler' => 'AboutController@index'],
    ['method' => 'GET', 'path' => '/governance', 'handler' => 'AboutController@governance'],
    ['method' => 'GET', 'path' => '/services', 'handler' => 'AboutController@services'],

    // Contact
    ['method' => 'GET', 'path' => '/contact', 'handler' => 'ContactController@index'],
    ['method' => 'POST', 'path' => '/contact', 'handler' => 'ContactController@submit'],

    // Authentication
    ['method' => 'GET', 'path' => '/login', 'handler' => 'AuthController@loginForm'],
    ['method' => 'POST', 'path' => '/login', 'handler' => 'AuthController@login'],
    ['method' => 'GET', 'path' => '/register', 'handler' => 'AuthController@registerForm'],
    ['method' => 'POST', 'path' => '/register', 'handler' => 'AuthController@register'],
    ['method' => 'GET', 'path' => '/logout', 'handler' => 'AuthController@logout'],

    // Projects
    ['method' => 'GET', 'path' => '/projects', 'handler' => 'ProjectController@index'],
    ['method' => 'GET', 'path' => '/projects/{id}', 'handler' => 'ProjectController@show'],
    ['method' => 'GET', 'path' => '/projects/submit', 'handler' => 'ProjectController@submitForm', 'middleware' => ['AuthMiddleware']],
    ['method' => 'POST', 'path' => '/projects/submit', 'handler' => 'ProjectController@submit', 'middleware' => ['AuthMiddleware']],

    // Marketplace
    ['method' => 'GET', 'path' => '/marketplace', 'handler' => 'MarketplaceController@index'],
    ['method' => 'GET', 'path' => '/marketplace/{id}', 'handler' => 'MarketplaceController@show'],

    // Investor Portal
    ['method' => 'GET', 'path' => '/investor', 'handler' => 'InvestorController@index', 'middleware' => ['AuthMiddleware', 'InvestorMiddleware']],
    ['method' => 'GET', 'path' => '/investor/kyc', 'handler' => 'InvestorController@kycForm', 'middleware' => ['AuthMiddleware']],
    ['method' => 'POST', 'path' => '/investor/kyc', 'handler' => 'InvestorController@kycSubmit', 'middleware' => ['AuthMiddleware']],
    ['method' => 'GET', 'path' => '/investor/data-room/{id}', 'handler' => 'InvestorController@dataRoom', 'middleware' => ['AuthMiddleware', 'InvestorMiddleware']],
    ['method' => 'POST', 'path' => '/investor/interest/{id}', 'handler' => 'InvestorController@expressInterest', 'middleware' => ['AuthMiddleware', 'InvestorMiddleware']],

    // Admin Dashboard
    ['method' => 'GET', 'path' => '/admin', 'handler' => 'AdminController@index', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/projects', 'handler' => 'AdminController@projects', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/projects/{id}/approve', 'handler' => 'AdminController@approveProject', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/projects/{id}/reject', 'handler' => 'AdminController@rejectProject', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/investors', 'handler' => 'AdminController@investors', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/investors/{id}/approve', 'handler' => 'AdminController@approveInvestor', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/statistics', 'handler' => 'AdminController@statistics', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Language switch
    ['method' => 'GET', 'path' => '/lang/{lang}', 'handler' => 'LanguageController@switch'],
];
