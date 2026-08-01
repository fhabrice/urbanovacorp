<?php

namespace App\Controllers;

class ContactController extends Controller
{
    public function index()
    {
        return $this->view('contact/index');
    }

    public function submit()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();

        $name = $request->getBodyParam('name');
        $email = $request->getBodyParam('email');
        $phone = $request->getBodyParam('phone');
        $company = $request->getBodyParam('company');
        $subject = $request->getBodyParam('subject');
        $message = $request->getBodyParam('message');
        $type = $request->getBodyParam('type', 'general');

        // Validate input
        if (empty($name) || empty($email) || empty($message)) {
            $session->setFlashMessage('error', __('contact.required_fields'));
            return $this->redirect('/contact');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $session->setFlashMessage('error', __('contact.invalid_email'));
            return $this->redirect('/contact');
        }

        // Save contact
        $db->execute(
            "INSERT INTO contacts (name, email, phone, company, subject, message, type, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'new')",
            [$name, $email, $phone, $company, $subject, $message, $type]
        );

        $session->setFlashMessage('success', __('contact.success'));
        return $this->redirect('/contact');
    }
}
