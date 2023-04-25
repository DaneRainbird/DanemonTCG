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
        if (is_null($searchQuery)) {
            // Redirect to the home page
            return redirect()->to('/');
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
            'searchQuery' => $searchQuery
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

    public function createCollection() {
        // Get form data
        $collectionName = $this->request->getPost('collection_name');
        $userId = $this->request->getPost('user_id');

        // Ensure that the user is signed in, and that the signed in uid matches the uid in the form
        if (!$this->session->get('uid') || $this->session->get('uid') != $userId) {
            $response = array("status" => "error", "message" => "User not authenticated");
            return json_encode($response);
        }

        // Create the collection
        if (!$this->db->createCollection($collectionName, $userId)) {
            $response = array("status" => "error", "message" => "Failed to create collection. Try again later!");
            return json_encode($response);
        }

        // Return a success message
        $response = array("status" => "success", "message" => "Collection created!", 'id' => $this->db->getInsertID());
        return json_encode($response);
        
    }

    /**
     * Add a card to a collection.
    */
    public function addToCollection() {
        // Get form data
        $cardId = $this->request->getPost('card_id');
        $collectionId = $this->request->getPost('collection_id');
        $userId = $this->request->getPost('user_id');

        // Ensure that the user is signed in, and that the signed in uid matches the uid in the form
        if (!$this->session->get('uid') || $this->session->get('uid') != $userId) {
            $response = array("status" => "error", "message" => "User not authenticated");
            $this->response->setStatusCode(401);
            return json_encode($response);
        }

        // Ensure that the user owns the collection
        if (!$this->db->userOwnsCollection($userId, $collectionId)) {
            $response = array("status" => "error", "message" => "User does not own the requested collection!");
            $this->response->setStatusCode(401);
            return json_encode($response);
        }

        // Ensure that the card is not already in the collection
        if ($this->db->cardInCollection($cardId, $collectionId)) {
            $response = array("status" => "error", "message" => "Card already in collection!");
            $this->response->setStatusCode(400);
            return json_encode($response);
        }

        // Add the card to the collection
        if (!$this->db->addCardToCollection($cardId, $collectionId)) {
            $response = array("status" => "error", "message" => "Failed to add card to collection. Try again later!");
            $this->response->setStatusCode(500);
            return json_encode($response);
        }
        
        // Return a success message
        $response = array("status" => "success", "message" => "Card added to collection!");
        return json_encode($response);

    }

}