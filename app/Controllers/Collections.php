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
}