<?php
    namespace App\Libraries;

    use Pokemon\Pokemon;

    class PokemonTCGService {

        // List of known query keywords
        private $knownQueryKeywords = [
            'name',
            'subtypes',
            'supertype',
            'types',
            'nationalPokedexNumbers',
            'hp',
            'rarity'
        ];

        /**
         * Constructor
         */
        public function __construct() {
            Pokemon::ApiKey(env("pokemon.apikey"));
        }

        /**
         * Parse a query.
         * 
         * @param string $query The query to parse
         * @return array The parsed query
         */
        private function parseQuery(string $query) : array {
            $queryParts = explode(':', $query);
            if (count($queryParts) == 2) {
                // Get the keyword and the value
                $keyword = $queryParts[0];
                $value = $queryParts[1];

                // Check if the keyword is known
                if (in_array($keyword, $this->knownQueryKeywords)) {
                    // Return the parsed query
                    return [
                        $keyword => $value
                    ];

                }
            } else {
                // Return a default query (i.e. search for the name of the card)
                return [
                    'name' => $query
                ];
            }
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
    }
?>