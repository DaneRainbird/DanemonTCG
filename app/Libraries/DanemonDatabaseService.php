<?php 

namespace App\Libraries;
use CodeIgniter\Database\Exceptions\DataException;
use Exception;

class DanemonDatabaseService {

            // Variable declarations 
            private $db;
    
            /**
             * Constructor, initialises the database
             * 
             * @return void
             */
            public function __construct() {
                // Initialise database
                $this->db = \Config\Database::connect(env('CI_ENVIRONMENT') === 'development' ? 'local' : 'default');
            }

            /**
             * Gets the last inserted ID from the database
             * 
             * @return int The last inserted ID
             */
            public function getInsertID() {
                return $this->db->insertID();
            }

            /**
             * Gets a user from the database by their Okta UID ("sub")
             * 
             * @param string $uid Okta UID
             * 
             * @return bool Returns true if the user exists, or false if not
             */
            public function doesUserExist($uid) {
                // Try to get the user, return error if it fails 
                $query = $this->db->table('users')->getWhere(['okta_uid' => $uid]);

                if ($query->getResult()) {
                    return true;
                }

                return false;
            }

            /**
             * Creates a new user in the database with their Okta UID ("sub") and email address
             * Defaults to creating a user with the "user" role (i.e. 0)
             * 
             * @param string $uid Okta UID
             * @param string $email Email address
             * 
             * @return mixed Returns true if successful, or the error message if not
             */
            public function createUser($uid, $email) {
                $data = [
                    'okta_uid' => $uid,
                    'email' => $email,
                    'role' => 0 // 0 = user, 1 = admin
                ];

                // Try to insert the user, return error if it fails 
                if (!$this->db->table('users')->insert($data)) {
                    throw new Exception($this->db->error()['message']);
                } 

                return true;
            }

            /**
             * Checks if the user with the specified Okta UID ("sub") is an admin
             * 
             * @param string $uid Okta UID
             * 
             * @return bool Returns true if the user is an admin, or false if not
             */
            public function isAdmin($uid) {
                // Try to get the user, return error if it fails 
                $query = $this->db->table('users')->getWhere(['okta_uid' => $uid]);

                if ($query->getResult()) {
                    $result = $query->getResult()[0];

                    if ($result->role == 1) {
                        return true;
                    }
                }

                return false;
            }

            /** 
             * Gets a collection's name by its ID
             * 
             * @param int $collectionId The ID of the collection to get the name of
             * 
             * @return string Returns the name of the collection
             */
            public function getCollectionName($collectionId) {
                // Get the collection name
                $query = $this->db->table('collections')->getWhere(['id' => $collectionId]);
                $result = $query->getResult();

                if ($result) {
                    return $result[0]->name;
                }

                return false;
            }

            /**
             * Gets all collections in the system (to be used by admins only)
             * 
             * @return array Returns an array of collection objects
             */
            public function getAllCollections() {
                // Get all collections
                $query = $this->db->table('collections')->get();
                
                // Append the Okta username of the collection owner to each collection object
                $collections = $query->getResult();
                foreach ($collections as $collection) {
                    $query = $this->db->table('users')->getWhere(['okta_uid' => $collection->okta_id]);
                    $collection->username = $query->getResult()[0]->email;
                }

                return $collections;
            }

            /**
             * Gets all of the cards in a collection 
             * 
             * @param int $collectionId The ID of the collection to get cards for
             * 
             * @return array Returns an array of card objects
             */
            public function getCardsInCollection($collectionId) {
                // Get the cards in the collection
                $query = $this->db->table('collection_cards')->getWhere(['collection_id' => $collectionId]);
                return $query->getResult();
            }

            /**
             * Gets every card in every collection owned by the user with the specified Okta user ID.
             * 
             * @param string $uid The Okta user ID of the user to get cards for.
             * 
             * @return array Returns an array of card objects.
             */
            public function getAllCardsInCollections($uid) {
                // Get the user's collections
                $collections = $this->getUserCollections($uid);

                // Get the cards in each collection
                $cards = [];
                foreach ($collections as $collection) {
                    $cards = array_merge($cards, $this->getCardsInCollection($collection->id));
                }

                return $cards;
            }

            /**
             * Checks if the user owns a collection with the specified ID.
             *
             * @param string $uid The Okta user ID of the user to check ownership for.
             * @param int $collectionId The ID of the collection to check ownership for.
             * @return bool Returns true if the user owns the collection, false otherwise.
             */
            public function userOwnsCollection($uid, $collectionId) {
                // Check if the user owns the collection
                $query = $this->db->table('collections')->getWhere(['okta_id' => $uid, 'id' => $collectionId]);
                if ($query->getResult()) {
                    return true;
                }

                return false;
            }

            /**
             * Gets all collections owned by the user with the specified Okta user ID.
             *
             * @param string $uid The Okta user ID of the user to get collections for.
             * @return array Returns an array of collection objects owned by the user, or an empty array if the user has no collections.
             */
            public function getUserCollections($uid) {
                // Get the user's collections based off their Okta UID
                $query = $this->db->table('collections')->getWhere(['okta_id' => $uid]);
                return $query->getResult();
            }

            /**
             * Gets all collections owned by the user with the specified Okta user ID, and includes the cards within each collection.
             *
             * @param string $uid The Okta user ID of the user to get collections for.
             * @return array Returns an array of collection objects owned by the user, with each collection object including its associated cards, or an empty array if the user has no collections.
             */
            public function getUserCollectionsWithCards($uid) {
                // Get the user's collections based off their Okta UID, and then also get the contents of each collection from the collection_cards table via the collection id
                $query = $this->db->table('collections')->getWhere(['okta_id' => $uid]);
                $collections = $query->getResult();

                foreach ($collections as $collection) {
                    $query = $this->db->table('collection_cards')->getWhere(['collection_id' => $collection->id]);
                    $collection->cards = $query->getResult();
                }

                return $collections;        
            }

            /**
             * Adds a card to the collection with the specified ID.
             *
             * @param int $card The ID of the card to add.
             * @param int $collectionId The ID of the collection to add the card to.
             * @return bool Returns true if the card was successfully added to the collection, or throws an exception if an error occurred.
             * @throws Exception Throws an exception if an error occurred while trying to insert the card.
             */
            public function addCardToCollection($card, $collectionId) {
                // Add the card to the collection
                $data = [
                    'collection_id' => $collectionId,
                    'card_id' => $card
                ];

                // Try to insert the card, return error if it fails 
                if (!$this->db->table('collection_cards')->insert($data)) {
                    throw new Exception($this->db->error()['message']);
                } 

                return true;
            }

            /**
             * Removes a card from the collection with the specified ID.
             *
             * @param int $card The ID of the card to remove.
             * @param int $collectionId The ID of the collection to remove the card from.
             * @return bool Returns true if the card was successfully removed from the collection, or throws an exception if an error occurred.
             * @throws Exception Throws an exception if an error occurred while trying to delete the card.
             */
            public function removeCardFromCollection($card, $collectionId) {
                // Remove the card from the collection
                $data = [
                    'collection_id' => $collectionId,
                    'card_id' => $card
                ];

                // Try to delete the card, return error if it fails 
                if (!$this->db->table('collection_cards')->delete($data)) {
                    throw new Exception($this->db->error()['message']);
                } 

                return true;
            }

            /**
             * Creates a collection with the specified name, owned by the user with the specified Okta user ID.
             * 
             * @param string $collectionName The name of the collection to create.
             * @param string $userId The Okta user ID of the user to create the collection for.
             * @return bool Returns true if the collection was successfully created, or throws an exception if an error occurred.
             * @throws Exception Throws an exception if an error occurred while trying to insert the collection.
             */
            public function createCollection($collectionName, $userId) {
                // Create the collection
                $data = [
                    'name' => $collectionName,
                    'okta_id' => $userId
                ];

                // Try to insert the collection, return error if it fails 
                if (!$this->db->table('collections')->insert($data)) {
                    throw new Exception($this->db->error()['message']);
                } 

                return true;
            }

            /**
             * Checks if the card with the specified ID is in the collection with the specified ID.
             * 
             * @param int $cardId The ID of the card to check.
             * @param int $collectionId The ID of the collection to check.
             * @return bool Returns true if the card is in the collection, false otherwise.
             */
            public function cardInCollection($cardId, $collectionId) {
                // Check if the card is in the collection
                $query = $this->db->table('collection_cards')->getWhere(['card_id' => $cardId, 'collection_id' => $collectionId]);
                if ($query->getResult()) {
                    return true;
                }

                return false;
            }
}