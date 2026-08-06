<?php

namespace App\Controllers;

use App\Core\Database;

class NewsController extends SimpleController
{
    protected $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = new Database($this->config['database']);
    }

    public function index()
    {
        $news = $this->database->fetchAll(
            "SELECT * FROM news WHERE status = 'published' ORDER BY published_at DESC"
        );

        return $this->view('news/index', [
            'newsItems' => $news,
        ]);
    }

    public function show($identifier)
    {
        if (is_numeric($identifier)) {
            $article = $this->database->fetchOne("SELECT * FROM news WHERE id = ? AND status = 'published'", [$identifier]);
        } else {
            $article = $this->database->fetchOne("SELECT * FROM news WHERE slug = ? AND status = 'published'", [$identifier]);
        }

        if (!$article) {
            http_response_code(404);
            return require APP_PATH . '/Views/errors/404.php';
        }

        return $this->view('news/show', [
            'article' => $article,
        ]);
    }
}
