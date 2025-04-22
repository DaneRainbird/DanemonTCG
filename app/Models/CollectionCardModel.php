<?php

namespace App\Models;

use CodeIgniter\Model;

class CollectionCardModel extends Model
{
    protected $table = 'collection_cards';
    protected $primaryKey = ['collection_id', 'card_id'];
    protected $allowedFields = ['collection_id', 'card_id'];
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useAutoIncrement = false;

    /**
     * Gets all cards in a collection
     *
     * @param int $collectionId Collection ID
     * @return array Card objects
     */
    public function getCardsInCollection($collectionId)
    {
        return $this->where('collection_id', $collectionId)->findAll();
    }

    /**
     * Gets all cards in all collections owned by a user
     *
     * @param string $uid Okta user ID
     * @return array Card objects
     */
    public function getAllCardsInCollections($uid)
    {
        $collectionModel = model(CollectionModel::class);
        $collections = $collectionModel->getUserCollections($uid);
        
        $cards = [];
        foreach ($collections as $collection) {
            $collectionCards = $this->where('collection_id', $collection['id'])->findAll();
            $cards = array_merge($cards, $collectionCards);
        }
        
        return $cards;
    }

    /**
     * Adds a card to a collection
     *
     * @param string $cardId Card ID
     * @param int $collectionId Collection ID
     * @return bool True on success, false on failure
     */
    public function addCardToCollection($cardId, $collectionId)
    {
        return $this->insert([
            'collection_id' => $collectionId,
            'card_id' => $cardId
        ]) !== false;
    }

    /**
     * Removes a card from a collection
     *
     * @param string $cardId Card ID
     * @param int $collectionId Collection ID
     * @return bool True on success, false on failure
     */
    public function removeCardFromCollection($cardId, $collectionId)
    {
        return $this->where([
            'card_id' => $cardId,
            'collection_id' => $collectionId
        ])->delete();
    }

    /**
     * Checks if a card is in a collection
     *
     * @param string $cardId Card ID
     * @param int $collectionId Collection ID
     * @return bool True if card is in collection, false otherwise
     */
    public function cardInCollection($cardId, $collectionId)
    {
        return $this->where([
            'card_id' => $cardId,
            'collection_id' => $collectionId
        ])->first() !== null;
    }
}