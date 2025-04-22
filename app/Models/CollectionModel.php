<?php

namespace App\Models;

use CodeIgniter\Model;

class CollectionModel extends Model
{
    protected $table = 'collections';
    protected $primaryKey = 'id';
    protected $allowedFields = ['okta_id', 'name'];

    /**
     * Gets a collection's name by its ID
     *
     * @param int $collectionId Collection ID
     * @return string|bool Collection name or false if not found
     */
    public function getCollectionName($collectionId)
    {
        $collection = $this->find($collectionId);
        return $collection ? $collection['name'] : false;
    }

    /**
     * Gets all collections in the system (admin only)
     *
     * @return array Collection objects with owner usernames
     */
    public function getAllCollections()
    {
        $collections = $this->findAll();
        $userModel = model(UserModel::class);
        
        foreach ($collections as &$collection) {
            $user = $userModel->where('okta_uid', $collection['okta_id'])->first();
            $collection['username'] = $user ? $user['email'] : 'Unknown';
        }
        
        return $collections;
    }

    /**
     * Gets all collections owned by a user
     *
     * @param string $uid Okta user ID
     * @return array Collection objects
     */
    public function getUserCollections($uid)
    {
        return $this->where('okta_id', $uid)->findAll();
    }

    /**
     * Gets all collections with cards for a user
     *
     * @param string $uid Okta user ID
     * @return array Collection objects with cards
     */
    public function getUserCollectionsWithCards($uid)
    {
        $collections = $this->where('okta_id', $uid)->findAll();
        $collectionCardModel = model(CollectionCardModel::class);
        
        foreach ($collections as &$collection) {
            $collection['cards'] = $collectionCardModel->getCardsInCollection($collection['id']);
        }
        
        return $collections;
    }

    /**
     * Checks if a user owns a collection
     *
     * @param string $uid Okta user ID
     * @param int $collectionId Collection ID
     * @return bool True if user owns collection, false otherwise
     */
    public function userOwnsCollection($uid, $collectionId)
    {
        return $this->where(['okta_id' => $uid, 'id' => $collectionId])->first() !== null;
    }

    /**
     * Creates a new collection
     *
     * @param string $collectionName Collection name
     * @param string $userId Okta user ID
     * @return mixed ID of new collection or false on failure
     */
    public function createCollection($collectionName, $userId)
    {
        return $this->insert([
            'name' => $collectionName,
            'okta_id' => $userId
        ]);
    }
}