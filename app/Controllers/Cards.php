<?php 

namespace App\Controllers;

use App\Libraries\PokemonTCGService;
use PHPUnit\Util\Type;

class Cards extends BaseController {

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
     * Index Page for the Cards controller.
     * 
     * @return string The rendered view
     */
    public function index() : string {
        echo view('fragments/html_head', [
            'title' => 'Home',
            'styles' => [
                'assets/css/main.css'
            ]
        ]);
        echo view('fragments/header');
        echo view('cards/index');
        return view('fragments/footer');
    }

    /**
     * Search for cards.
     * 
     */
    public function search() {
        // Get the search query
        $searchQuery = $this->request->getPost('search-value');

        // Convert the search query to a string
        $searchQuery = (string) $searchQuery;

        // Get the cards
        $cards = $this->pokemonTCGService->search($searchQuery);
        
        // Render the view
        echo view('fragments/html_head', [
            'title' => 'Search',
            'styles' => [
                '/assets/css/main.css'
            ]
        ]);
        echo view('fragments/header');
        echo view('cards/results', [
            'cards' => $cards
        ]);
        return view('fragments/footer');
    }
}