<?php 

namespace App\Controllers;

use App\Libraries\PokemonTCGService;

class Collections extends BaseController {

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
     * View a collection.
     * 
     * @param int $collectionId The ID of the collection to view.
     */
    public function view($collectionId) {
        // Check if the user is signed in
        if (!$this->session->get('uid')) {
            session()->setFlashdata('error', 'You are not signed in!');
            return redirect()->to('/');
        }

        // If the user is not an admin or does not own the collection, redirect to the home page
        if ($this->session->get('isAdmin') === "false" && !$this->db->userOwnsCollection($this->session->get('uid'), $collectionId)) {
            session()->setFlashdata('error', 'You do not have permission to view this collection!');
            return redirect()->to('/');
        }

        // Get the cards in the collection
        $results = $this->db->getCardsInCollection($collectionId);

        // Loop through each card and get it's details
        $cards = [];
        foreach ($results as $result) {
            array_push($cards, $this->pokemonTCGService->getCard($result->card_id, 'id,name,number,images,set'));
        }

        // Get the collection name
        $collectionName = $this->db->getCollectionName($collectionId);

        // See how many cards are in the collection
        $collectionCardCount = count($cards);

        // If the number of cards is <= 5, we want to change the cardsPerRow value automatically (unless overruled by an existing URL param)
        if ($collectionCardCount <= 5 && !$this->request->getGet('cards_per_row')) {
            $cardsPerRow = $collectionCardCount;
        } else {
            $cardsPerRow = $this->request->getGet('cards_per_row') ?? 5;
        }

        // Render the card search results view
        echo view('fragments/html_head', [
            'title' => $collectionName,
            'styles' => [
                '/assets/css/main.css'
            ]
        ]);
        echo view('fragments/header');
        echo view('cards/results', [
            'cards' => $cards,
            'collectionId' => $collectionId,
            'collectionName' => $collectionName,
            'searchQuery' => '',
            'isSearch' => false,
            'isCollection' => true,
            'view' => $this->request->getGet('view') === 'table' ? 'table' : 'grid',
            'cardsPerRow' => $cardsPerRow
        ]);
        return view('fragments/footer');
    }

    public function viewAll() {
        // Ensure that the user is signed in, and that they own this collection
        if (!$this->session->get('uid')) {
            session()->setFlashdata('error', 'You must be signed in to view this page!');
            return redirect()->to('/');
        }

        // Get all of the cards in all of the user's collections
        $results = $this->db->getAllCardsInCollections($this->session->get('uid'));

        // Loop through each card and get it's details
        $cards = [];
        foreach ($results as $result) {
            array_push($cards, $this->pokemonTCGService->getCard($result->card_id, 'id,name,number,images,set'));
        }

        // Render the card search results view
        echo view('fragments/html_head', [
            'title' => 'Search',
            'styles' => [
                '/assets/css/main.css'
            ]
        ]);
        echo view('fragments/header');
        echo view('collections/results', [
            'cards' => $cards,
            'collectionId' => 'user-all',
            'collectionName' => "All Collections"
        ]);
        return view('fragments/footer');
    }

    /**
     * Creates a new collection.
     */
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

    /**
     * Remove a card from a collection.
     */
    public function removeFromCollection() {
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

        // If the collection-id is 'user-all', then the user is trying to remove a card from all of their collections
        if ($collectionId == 'user-all') {
            // Get all of the user's collections
            $collections = $this->db->getUserCollections($userId);

            // Loop through each collection and remove the card from it
            foreach ($collections as $collection) {
                // Ensure that the card is in the collection
                if ($this->db->cardInCollection($cardId, $collection->id)) {
                    // If it is, remove it
                    if (!$this->db->removeCardFromCollection($cardId, $collection->id)) {
                        $response = array("status" => "error", "message" => "Failed to remove card from collection. Try again later!");
                        $this->response->setStatusCode(500);
                        return json_encode($response);
                    }
                }
            }

            // Return a success message
            $response = array("status" => "success", "message" => "Card removed from all collections!");
            return json_encode($response);

        } else {
            // Ensure that the user owns the collection
            if (!$this->db->userOwnsCollection($userId, $collectionId)) {
                $response = array("status" => "error", "message" => "User does not own the requested collection!");
                $this->response->setStatusCode(401);
                return json_encode($response);
            }

            // Ensure that the card is in the collection
            if (!$this->db->cardInCollection($cardId, $collectionId)) {
                $response = array("status" => "error", "message" => "Card not in collection!");
                $this->response->setStatusCode(400);
                return json_encode($response);
            }

            // Remove the card from the collection
            if (!$this->db->removeCardFromCollection($cardId, $collectionId)) {
                $response = array("status" => "error", "message" => "Failed to remove card from collection. Try again later!");
                $this->response->setStatusCode(500);
                return json_encode($response);
            }
        }

        // Return a success message
        $response = array("status" => "success", "message" => "Card removed from collection!");
        return json_encode($response);
    }


}