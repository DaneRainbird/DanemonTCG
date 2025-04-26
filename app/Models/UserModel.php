<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['okta_uid', 'email', 'role'];

    /**
     * Checks if a user exists based on their Okta UID
     *
     * @param string $uid Okta UID
     * @return bool True if user exists, false otherwise
     */
    public function doesUserExist($uid)
    {
        return $this->where('okta_uid', $uid)->first() !== null;
    }

    /**
     * Creates a new user with the specified Okta UID and email
     *
     * @param string $uid Okta UID
     * @param string $email Email address
     * @return mixed ID of the newly created user or false on failure
     */
    public function createUser($uid, $email)
    {
        $data = [
            'okta_uid' => $uid,
            'email' => $email,
            'role' => 0 // 0 = user, 1 = admin, defaulting to user.
        ];

        return $this->insert($data);
    }

    /**
     * Checks if the user with the specified Okta UID is an admin
     *
     * @param string $uid Okta UID
     * @return bool True if user is admin, false otherwise
     */
    public function isAdmin($uid)
    {
        $user = $this->where('okta_uid', $uid)->first();
        return $user !== null && $user['role'] == 1;
    }
}