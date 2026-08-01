<?php

function __($key)
{
    static $translations = [];
    $session = $_SESSION ?? [];
    $lang = $session['language'] ?? 'fr';

    if (!isset($translations[$lang])) {
        $file = APP_PATH . '/Helpers/lang/' . $lang . '.php';
        if (file_exists($file)) {
            $translations[$lang] = require $file;
        } else {
            $translations[$lang] = require APP_PATH . '/Helpers/lang/fr.php'; // Fallback to French
        }
    }

    $keys = explode('.', $key);
    $value = $translations[$lang];

    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $key; // Return key if translation not found
        }
    }

    return is_array($value) ? $key : $value;
}
