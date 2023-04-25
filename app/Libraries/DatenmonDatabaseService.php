<?php 

namespace App\Libraries;
use CodeIgniter\Database\Exceptions\DataException;
use Exception;

class DatenmonDatabaseService {

            // Variable declarations 
            private $db;
    
            /**
             * __construct
             */
            public function __construct() {
                // Initialise database
                $this->db = \Config\Database::connect();
            }

            /**
             * doesUserExist
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
             * createUser
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

            public function userOwnsCollection($uid, $collectionId) {
                // Check if the user owns the collection
                $query = $this->db->table('collections')->getWhere(['okta_id' => $uid, 'id' => $collectionId]);
                if ($query->getResult()) {
                    return true;
                }

                return false;
            }

            public function getUserCollections($uid) {
                // Get the user's collections based off their Okta UID
                $query = $this->db->table('collections')->getWhere(['okta_id' => $uid]);
                return $query->getResult();
            }

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