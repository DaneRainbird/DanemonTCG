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
}