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
}
