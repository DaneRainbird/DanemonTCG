<?php 

namespace App\Controllers;

use App\Libraries\PokemonTCGService;

class Sets extends BaseController {

    /**
     * The PokemonTCGService instance.
     * 
     * @var PokemonTCGService
     */
    private $pokemonTCGService;

    /**
     * Constructor
     */
    public function __construct() {
        helper('html');
        $this->pokemonTCGService = new PokemonTCGService();
    }

    /**
     * Display the sets page.
     * 
     */
    public function index() : string {
        // Get the sets
        $sets = $this->pokemonTCGService->getSets();

        echo view('fragments/html_head', [
            'title' => 'Sets',
            'styles' => [
                'assets/css/main.css'
            ]
        ]);
        echo view('fragments/header');
        echo view('sets/index', [
            'sets' => $sets
        ]);
        return view('fragments/footer');
    }

    /**
     * Display the cards of a set.
     * 
     * @param string $setCode The set code
     */
    public function details(string $setCode) : string {
        // Get the set
        $results = $this->pokemonTCGService->search('set.id:' . $setCode);

        echo view('fragments/html_head', [
            'title' => 'Set Details',
            'styles' => [
                '/assets/css/main.css'
            ]
        ]);
        echo view('fragments/header');
        echo view('cards/results', [
            'searchQuery' => 'set.id:' . $setCode,
            'cards' => $results['cards'],
            'pagination' => $results['pagination'],
        ]);
        return view('fragments/footer');
    }
}