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
    // Promoter Dashboard
    ['method' => 'GET', 'path' => '/promoter', 'handler' => 'PromoterController@index', 'middleware' => ['AuthMiddleware', 'PromoterMiddleware']],
    // Marketplace — public sans compte (§1)
    ['method' => 'GET', 'path' => '/marketplace', 'handler' => 'MarketplaceController@index'],
    ['method' => 'GET', 'path' => '/marketplace/{id}', 'handler' => 'MarketplaceController@show'],
    ['method' => 'POST', 'path' => '/marketplace/{id}/interest', 'handler' => 'InvestorController@expressInterest', 'middleware' => ['AuthMiddleware']],
    ['method' => 'GET', 'path' => '/debug/marketplace', 'handler' => 'MarketplaceController@debug'],
    // Investor Portal
    ['method' => 'GET', 'path' => '/investor', 'handler' => 'InvestorController@index', 'middleware' => ['AuthMiddleware', 'InvestorMiddleware']],
    ['method' => 'GET', 'path' => '/investor/kyc', 'handler' => 'InvestorController@kycForm', 'middleware' => ['AuthMiddleware']],
    ['method' => 'POST', 'path' => '/investor/kyc', 'handler' => 'InvestorController@kycSubmit', 'middleware' => ['AuthMiddleware']],
    ['method' => 'GET', 'path' => '/investor/profile', 'handler' => 'InvestorController@profileForm', 'middleware' => ['AuthMiddleware', 'InvestorMiddleware']],
    ['method' => 'POST', 'path' => '/investor/profile', 'handler' => 'InvestorController@profileSubmit', 'middleware' => ['AuthMiddleware', 'InvestorMiddleware']],
    ['method' => 'GET', 'path' => '/investor/data-room/{id}', 'handler' => 'InvestorController@dataRoom', 'middleware' => ['AuthMiddleware', 'InvestorMiddleware']],
    ['method' => 'POST', 'path' => '/investor/interest/{id}', 'handler' => 'InvestorController@expressInterest', 'middleware' => ['AuthMiddleware']],
    ['method' => 'GET', 'path' => '/investor/interest/{id}', 'handler' => 'InvestorController@expressInterest', 'middleware' => ['AuthMiddleware']],
    ['method' => 'GET', 'path' => '/investor/activate', 'handler' => 'InvestorController@activate', 'middleware' => ['AuthMiddleware']],
    ['method' => 'POST', 'path' => '/investor/activate', 'handler' => 'InvestorController@activate', 'middleware' => ['AuthMiddleware']],
    ['method' => 'GET', 'path' => '/investor/favorites/{id}/add', 'handler' => 'InvestorController@addFavorite', 'middleware' => ['AuthMiddleware', 'InvestorMiddleware']],
    ['method' => 'GET', 'path' => '/investor/favorites/{id}/remove', 'handler' => 'InvestorController@removeFavorite', 'middleware' => ['AuthMiddleware', 'InvestorMiddleware']],
    ['method' => 'GET', 'path' => '/investor/messages', 'handler' => 'InvestorController@messages', 'middleware' => ['AuthMiddleware', 'InvestorMiddleware']],
    ['method' => 'POST', 'path' => '/investor/messages/send', 'handler' => 'InvestorController@sendMessage', 'middleware' => ['AuthMiddleware', 'InvestorMiddleware']],

    // ===== ADMIN - TABLEAU DE BORD (Spec 4) =====
    ['method' => 'GET', 'path' => '/admin', 'handler' => 'AdminController@index', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/dashboard', 'handler' => 'AdminController@dashboard', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/executive', 'handler' => 'AdminController@executive', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/search', 'handler' => 'AdminController@search', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/audit-log', 'handler' => 'AdminController@auditLog', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/export', 'handler' => 'AdminController@export', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Utilisateurs (Spec 19)
    ['method' => 'GET', 'path' => '/admin/users', 'handler' => 'AdminController@users', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/users/{id}', 'handler' => 'AdminController@userView', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/users/{id}/toggle', 'handler' => 'AdminController@toggleUserStatus', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/users/{id}/reset-password', 'handler' => 'AdminController@resetPassword', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Projets (Spec 21-26)
    ['method' => 'GET', 'path' => '/admin/projects', 'handler' => 'AdminController@projects', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/projects/{id}', 'handler' => 'AdminController@projectShow', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/projects/{id}/review', 'handler' => 'AdminController@projectReview', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/projects/{id}/approve', 'handler' => 'AdminController@approveProject', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/projects/{id}/approve', 'handler' => 'AdminController@approveProject', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/projects/{id}/reject', 'handler' => 'AdminController@rejectProject', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/projects/{id}/reject', 'handler' => 'AdminController@rejectProject', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/projects/{id}/request-info', 'handler' => 'AdminController@requestProjectInfo', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/projects/{id}/delete', 'handler' => 'AdminController@deleteProject', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Investisseurs (Spec 27-30)
    ['method' => 'GET', 'path' => '/admin/investors', 'handler' => 'AdminController@investors', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/investors/{id}', 'handler' => 'AdminController@investorShow', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/investors/{id}/approve', 'handler' => 'AdminController@approveInvestor', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/investors/{id}/request-info', 'handler' => 'AdminController@requestInvestorInfo', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/investors/{id}/reject', 'handler' => 'AdminController@rejectInvestor', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/investors/{id}/reject', 'handler' => 'AdminController@rejectInvestor', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Levées de fonds (Spec 31-33)
    ['method' => 'GET', 'path' => '/admin/fundraising', 'handler' => 'AdminController@fundraising', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/fundraising/{id}', 'handler' => 'AdminController@fundraisingShow', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Investissements (Spec 34-37)
    ['method' => 'GET', 'path' => '/admin/investments', 'handler' => 'AdminController@investments', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/investments/{id}', 'handler' => 'AdminController@investmentShow', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/investments/{id}/confirm', 'handler' => 'AdminController@confirmInvestment', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Commissions (Spec 38-39)
    ['method' => 'GET', 'path' => '/admin/commissions', 'handler' => 'AdminController@commissions', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Data Room (Spec 40-45)
    ['method' => 'GET', 'path' => '/admin/data-rooms', 'handler' => 'AdminController@dataRooms', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/data-rooms/{id}', 'handler' => 'AdminController@dataRoomShow', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/data-rooms/access/{id}/approve', 'handler' => 'AdminController@approveDataRoomAccess', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Demandes (Spec 46-49)
    ['method' => 'GET', 'path' => '/admin/requests', 'handler' => 'AdminController@requests', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/requests/{id}/assign', 'handler' => 'AdminController@assignRequest', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Visites / Réservations (Spec 50)
    ['method' => 'GET', 'path' => '/admin/appointments', 'handler' => 'AdminController@appointments', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Notifications (Spec 51)
    ['method' => 'GET', 'path' => '/admin/notifications', 'handler' => 'AdminController@notifications', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Rapports (Spec 56)
    ['method' => 'GET', 'path' => '/admin/reports', 'handler' => 'AdminController@reports', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/statistics', 'handler' => 'AdminController@statistics', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Contenus (Spec 13)
    ['method' => 'GET', 'path' => '/admin/news', 'handler' => 'AdminController@news', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/news/create', 'handler' => 'AdminController@createNews', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/news/create', 'handler' => 'AdminController@storeNews', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/news/{id}/edit', 'handler' => 'AdminController@editNews', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/news/{id}/edit', 'handler' => 'AdminController@updateNews', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/news/{id}/delete', 'handler' => 'AdminController@deleteNews', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Admin - Administration & Paramètres (Spec 61-63)
    ['method' => 'GET', 'path' => '/admin/settings', 'handler' => 'AdminController@settings', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/settings/admin-users', 'handler' => 'AdminController@adminUsers', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // ===== CRM (Spec 6-18) =====
    ['method' => 'GET', 'path' => '/admin/crm/leads', 'handler' => 'CrmController@leads', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/leads/create', 'handler' => 'CrmController@createLead', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/crm/leads/create', 'handler' => 'CrmController@storeLead', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/leads/{id}/edit', 'handler' => 'CrmController@editLead', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/crm/leads/{id}/edit', 'handler' => 'CrmController@updateLead', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/leads/{id}/delete', 'handler' => 'CrmController@deleteLead', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/leads/{id}/convert', 'handler' => 'CrmController@convertLead', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/contacts', 'handler' => 'CrmController@contacts', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/contacts/{id}', 'handler' => 'CrmController@contactShow', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/companies', 'handler' => 'CrmController@companies', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/companies/create', 'handler' => 'CrmController@createCompany', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/crm/companies/create', 'handler' => 'CrmController@storeCompany', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/opportunities', 'handler' => 'CrmController@opportunities', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/opportunities/create', 'handler' => 'CrmController@createOpportunity', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/crm/opportunities/create', 'handler' => 'CrmController@storeOpportunity', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/opportunities/{id}/move', 'handler' => 'CrmController@moveOpportunityStage', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/crm/opportunities/{id}/move', 'handler' => 'CrmController@moveOpportunityStage', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/pipeline', 'handler' => 'CrmController@pipeline', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/activities', 'handler' => 'CrmController@activities', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/crm/activities/create', 'handler' => 'CrmController@storeActivity', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/tasks', 'handler' => 'CrmController@tasks', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/tasks/create', 'handler' => 'CrmController@createTask', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'POST', 'path' => '/admin/crm/tasks/create', 'handler' => 'CrmController@storeTask', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/tasks/{id}/status', 'handler' => 'CrmController@updateTaskStatus', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],


    // CRM - Relances & Rendez-vous alias (Spec 6.1)
    ['method' => 'GET', 'path' => '/admin/crm/relances', 'handler' => 'CrmController@tasks', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/rendez-vous', 'handler' => 'AdminController@appointments', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],
    ['method' => 'GET', 'path' => '/admin/crm/appointments', 'handler' => 'AdminController@appointments', 'middleware' => ['AuthMiddleware', 'AdminMiddleware']],

    // Language switch
    ['method' => 'GET', 'path' => '/lang/{lang}', 'handler' => 'LanguageController@switch'],
];
