<?php

namespace App\Controllers;

class HomeController extends SimpleController
{
    public function index()
    {
        // Static data for now (without database)
        $stats = [
            'projects_completed' => 12,
            'total_investments' => 2500000,
            'housing_units' => 450,
            'jobs_created' => 320,
        ];

        $featuredProjects = [
            [
                'id' => 1,
                'title' => 'Résidence Kinshasa Heights',
                'city' => 'Kinshasa',
                'country' => 'RD Congo',
                'funding_sought' => 500000,
                'roi' => 15.5,
                'image' => 'placeholder.jpg'
            ],
            [
                'id' => 2,
                'title' => 'Centre Commercial Lubumbashi',
                'city' => 'Lubumbashi',
                'country' => 'RD Congo',
                'funding_sought' => 1200000,
                'roi' => 18.2,
                'image' => 'placeholder.jpg'
            ],
            [
                'id' => 3,
                'title' => 'Eco-Quartier Goma',
                'city' => 'Goma',
                'country' => 'RD Congo',
                'funding_sought' => 800000,
                'roi' => 14.8,
                'image' => 'placeholder.jpg'
            ]
        ];

        return $this->view('home/index', [
            'stats' => $stats,
            'featuredProjects' => $featuredProjects,
        ]);
    }
}
