<?php 

namespace App\Controllers;

use App\Libraries\PokemonTCGService;

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
     */
    public function search() {
        // Get the search query from the GET params
        $searchQuery = $this->request->getGet('value');

        // Ensure there is a search query
        if (is_null($searchQuery) || empty($searchQuery) || $searchQuery === '') {
            // Set flash data
            $this->session->setFlashdata('error', 'Please enter a search query!');
            
            // Redirect to the home page
            return redirect()->to('/cards');
        }

        // Convert the search query to a string
        $searchQuery = (string) $searchQuery;

        // Get pagination details from the GET params
        $page = $this->request->getGet('page');
        $pageSize = $this->request->getGet('pageSize');
        
        // Ensure the pagination details are valid
        if (!is_null($page) && !is_null($pageSize)) {
            // Convert the pagination details to integers
            $page = (int) $page;
            $pageSize = (int) $pageSize;

            // Ensure the pagination details are valid
            if ($page < 1 || $pageSize < 1) {
                // Redirect to the home page
                return redirect()->to('/');
            }
        }
        
        // Get the cards and pagination data
        $results = $this->pokemonTCGService->search($searchQuery, $page, $pageSize);
        
        // Render the view
        echo view('fragments/html_head', [
            'title' => 'Search',
            'styles' => [
                '/assets/css/main.css'
            ]
        ]);
        echo view('fragments/header');
        echo view('cards/results', [
            'cards' => $results['cards'],
            'pagination' => $results['pagination'],
            'searchQuery' => $searchQuery,
            'view' => $this->request->getGet('view') === 'table' ? 'table' : 'grid'
        ]);
        return view('fragments/footer');
    }

    /**
     * Get the details of a card.
     * 
     * @param string $id The card ID
     */
    public function details(string $id) {
        // Get the card
        $card = $this->pokemonTCGService->getCard($id);

        // Get user collection data if the user is logged in
        if ($this->session->get('uid')) {
            $userCollections = $this->db->getUserCollections($this->session->get('uid'), $id);
        } else {
            $userCollections = [];
        }

        // Ensure the card exists
        if (is_null($card)) {
            // Redirect to the home page
            return redirect()->to('/');
        }

        // Render the view
        echo view('fragments/html_head', [
            'title' => 'Card Details',
            'styles' => [
                '/assets/css/main.css'
            ]
        ]);
        echo view('fragments/header');
        echo view('cards/details', [
            'card' => $card,
            'collections' => $userCollections
        ]);
        return view('fragments/footer');
    }

}