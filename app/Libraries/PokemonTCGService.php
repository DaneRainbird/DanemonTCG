<?php
    namespace App\Libraries;

    use Pokemon\Pokemon;

    class PokemonTCGService {

        // List of known query keywords
        private $knownQueryKeywords = [
            'name',
            'subtypes',
            'type',
            'nationalPokedexNumbers',
            'hp'
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
                // Return the parsed query
                return [
                    'name' => $query
                ];
            }
        }

        /**
         * Search for cards.
         * 
         * @param string $query The query to search for
         * @return array The results
         */
        public function search(string $query) : array {
            // Parse the query
            $parsedQuery = $this->parseQuery($query);
            // Search for cards
            $cards = Pokemon::Card()->where($parsedQuery)->all();
            // Return the cards
            return $cards;
        }
    }
?>