<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        echo view('fragments/html_head', [
            'title' => 'Home',
            'styles' => [
                'assets/css/main.css'
            ]
        ]);
        echo view('fragments/header');
        echo view('home');
        return view('fragments/footer');
    }

    public function about() {
        echo view('fragments/html_head', [
            'title' => 'About',
            'styles' => [
                '/assets/css/main.css'
            ]
        ]);
        echo view('fragments/header');
        echo view('about');
        return view('fragments/footer');
    }

    public function queries() {
        echo view('fragments/html_head', [
            'title' => 'About - Queries',
            'styles' => [
                '/assets/css/main.css'
            ]
        ]);
        echo view('fragments/header');
        echo view('queries');
        return view('fragments/footer');
    }
}
