<?php 

namespace App\Libraries;
use CodeIgniter\Database\Exceptions\DataException;
use Exception;

class DatenmonDatabaseService {

            // Variable declarations 
            private $db;
    
            /**
             * Constructor, initialises the database
             * 
             * @return void
             */
            public function __construct() {
                // Initialise database
                $this->db = \Config\Database::connect();
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
             * 
             * @param string $uid Okta UID
             * @param string $email Email address
             * 
             * @return mixed Returns true if successful, or the error message if not
             */
            public function createUser($uid, $email) {
                $data = [
                    'okta_uid' => $uid,
                    'email' => $email
                ];

                // Try to insert the user, return error if it fails 
                if (!$this->db->table('users')->insert($data)) {
                    throw new Exception($this->db->error()['message']);
                } 

                return true;
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
}