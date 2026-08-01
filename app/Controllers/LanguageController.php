<?php

namespace App\Controllers;

class LanguageController extends Controller
{
    public function switch($lang)
    {
        $session = $this->getSession();
        $config = $this->app->getConfig();

        // Validate language
        if (!in_array($lang, $config['languages']['available'])) {
            $lang = $config['languages']['default'];
        }

        // Set language in session
        $session->set('language', $lang);

        // Redirect back
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return $this->redirect($referer);
    }
}
