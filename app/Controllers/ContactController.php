<?php

namespace App\Controllers;

class ContactController extends SimpleController
{
    public function index()
    {
        return $this->view('contact/index');
    }

    public function submit()
    {
        // Simple submission without database
        $_SESSION['flash_success'] = 'Votre message a été envoyé avec succès.';
        $this->redirect('/?route=contact');
    }
}
