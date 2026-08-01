<?php

namespace App\Controllers;

class AboutController extends SimpleController
{
    public function index()
    {
        return $this->view('about/index');
    }

    public function governance()
    {
        return $this->view('about/governance');
    }

    public function services()
    {
        return $this->view('about/services');
    }
}
