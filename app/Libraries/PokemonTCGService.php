<?php
    namespace App\Libraries;

    use Pokemon\Pokemon;

    class PokemonTCGService {

        // List of known query keywords, pulled from https://docs.pokemontcg.io/api-reference/cards/get-card
        private $knownQueryKeywords = [
            'name',
            'subtypes',
            'supertype',
            'types',
            'rules',
            'attacks.name',
            'weaknesses.type',
            'retreatCost',
            'nationalPokedexNumbers',
            'hp',
            'rarity',
            'set.id',
            'set.name',
            'set.series',
            'set.ptcgoCode',
            'number',
            'artist'
        ];

        /**
         * Constructor
         */
        public function __construct() {
            Pokemon::ApiKey(env("pokemon.apikey"));
        }

        /**
         * Parses a query string into an array of key-value pairs.
         * 
         * @param string $query The query to parse
         * @return array The parsed query
         */
        private function parseQuery(string $query) : array {
            $queryParts = explode(' ', $query);
            $parsedQuery = [];
        
            foreach ($queryParts as $part) {
                $pos = strpos($part, ':');
                if ($pos !== false) {
                    $keyword = substr($part, 0, $pos);
                    $value = substr($part, $pos + 1);
        
                    if (in_array($keyword, $this->knownQueryKeywords)) {
                        $parsedQuery[$keyword] = $value;
                    }
                }
            }
        
            if (empty($parsedQuery)) {
                // Default query if no valid query parts were found
                $parsedQuery['name'] = $query;
            }
        
            return $parsedQuery;
        }

        /**
         * Search for cards.
         * 
         * @param string $query The query to search for
         * @param int $page The page number
         * @param int $pageSize The page size
         * @return array The results
         */
        public function search(string $query, int $page = null, int $pageSize = null) : array {
            // Parse the query
            $parsedQuery = $this->parseQuery($query);

            // Check if the page number is valid
            if ($page < 1 || is_null($page)) {
                $page = 1;
            }
            
            // Check if the page size is valid
            if ($pageSize < 1 || is_null($pageSize)) {
                $pageSize = 25;
            }

            // Search for cards
            $result = Pokemon::Card()->where($parsedQuery)->page($page)->pageSize($pageSize)->all();
            $paginationData = Pokemon::Card()->where($parsedQuery)->page($page)->pageSize($pageSize)->pagination();

            $cards = [];

            // Get the cards
            foreach ($result as $card) {
                array_push($cards, $card->toArray());
            }

            // Return the cards
            return [
                'cards' => $cards,
                'pagination' => $paginationData
            ];
        }

        /**
         * Get a card by its ID.
         * 
         * @param string $id The ID of the card
         * @return array The card
         */
        public function getCard(string $id) : array {
            // Get the card
            $card = Pokemon::Card()->find($id);

            // Return the card
            return $card->toArray();
        }

        /**
         * Get all the sets.
         * 
         * @return array The sets
         */
        public function getSets() : array {
            // Get the sets
            $sets = Pokemon::Set()->all();

            // Return the sets
            return $sets;
        }
    }
?>