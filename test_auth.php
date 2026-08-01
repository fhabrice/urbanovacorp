<?php
// Simple test for auth API
session_start();

// Simulate a logged in user
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';
$_SESSION['user_email'] = 'test@example.com';
$_SESSION['user_role'] = 'investor';

echo json_encode([
    'success' => true,
    'authenticated' => true,
    'is_investor' => true,
    'user' => [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role'],
        'initials' => substr($_SESSION['user_name'], 0, 2)
    ]
]);
