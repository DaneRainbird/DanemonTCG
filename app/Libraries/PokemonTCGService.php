<?php
    namespace App\Libraries;

    use Pokemon\Pokemon;
    use Pokemon\Models\Pagination;

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
        private function parseQuery(string $query): array {
            $parsedQuery = [];
            $pattern = '/(\w[\w.]*):(".*?"|\S+)/';
        
            // Find all query parts
            preg_match_all($pattern, $query, $matches, PREG_SET_ORDER);
        
            // Loop through the query parts and parse them
            foreach ($matches as $match) {
                $keyword = $match[1];
                $value = trim($match[2], '"');

                if (in_array($keyword, $this->knownQueryKeywords)) {

                    // If the value is a comma-separated list (e.g. types:fire,water), then split it into an array
                    if (strpos($value, ',') !== false) {
                        $tempValue = explode(',', $value);
                        
                        // If the value list does not contain an operator ("AND" or "OR", case-insensitive) in it anywhere, then add "AND" as the default operator
                        if (!preg_grep('/\b(AND|OR)\b/i', $tempValue)) {
                            array_unshift($tempValue, 'AND');
                        } else {
                            // Move the operator to the start of the array
                            $operator = preg_grep('/\b(AND|OR)\b/i', $tempValue);
                            $tempValue = array_merge($operator, array_diff($tempValue, $operator));
                        }
                        $value = $tempValue;
                    }

                    $parsedQuery[$keyword] = $value;
                }

                // Remove the query part from the query string
                $query = str_replace($match[0], '', $query);
            }

            // For whatever remains of the query string, assume it's the name
            $query = trim($query);
            if (!empty($query)) {
                $parsedQuery['name'] = $query;
            }
        
            // If no valid query parts were found, assume the query is the name
            if (empty($parsedQuery) && !empty($query)) {
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

            // If the query is empty (i.e. it failed to be parsed at all) return an search that returns no results
            if (empty($parsedQuery)) {
                // Create a pagination object that returns no results (so the UI doesn't break)
                $pagination = new Pagination();
                $pagination->setPage(1);
                $pagination->setPageSize(0);
                $pagination->setCount(0);
                $pagination->setTotalCount(0);

                return [
                    'cards' => [],
                    'pagination' => $pagination
                ];
            }

            // Check if this query and it's results exist in cache already
            $cacheKey = 'search_' . $query . '-' . $page . '-' . $pageSize;

            // Generate a cache key for the query by hashing it (prevents cache key length issues / invalid characters)
            $cacheKey = md5($cacheKey);
            $cachedResults = cache($cacheKey);

            // If the results exist in cache, return them
            if ($cachedResults) {
                return $cachedResults;
            } else {
                // Check if the page number is valid
                if ($page < 1 || is_null($page)) {
                    $page = 1;
                }
                
                // Check if the page size is valid
                if ($pageSize < 1 || is_null($pageSize)) {
                    $pageSize = 25;
                }

                // If the parsed query contains an array under one of it's values, then split it into something along the lines of types:[AND, fire, water] -> types:fire AND types:water
                foreach ($parsedQuery as $key => $value) {
                    if (is_array($value)) {
                        $tempValue = [];
                        foreach ($value as $item) {
                            if (is_string($item)) {
                                array_push($tempValue, $item);
                            }
                        }
                        $parsedQuery[$key] = $tempValue;
                    }
                }

                // Search for cards
                $result = Pokemon::Card()->where($parsedQuery)->page($page)->pageSize($pageSize)->all();
                $paginationData = Pokemon::Card()->where($parsedQuery)->page($page)->pageSize($pageSize)->pagination();

                $cards = [];

                // Get the cards
                foreach ($result as $card) {
                    array_push($cards, $card->toArray());
                }

                // Cache the results
                cache()->save($cacheKey, [
                    'cards' => $cards,
                    'pagination' => $paginationData
                ], 3600);

                // Return the cards
                return [
                    'cards' => $cards,
                    'pagination' => $paginationData
                ];
            }

        }

        /**
         * Get a card by its ID.
         * 
         * @param string $id The ID of the card
         * @return array The card
         */
        public function getCard(string $id) : array {
            // Check if the card exists in cache first
            $cachedCard = cache('card_' . $id);
            if ($cachedCard) {
                return $cachedCard;
            }

            // If not, get the card from the API
            $card = Pokemon::Card()->find($id);

            // Cache the card
            cache()->save('card_' . $id, $card->toArray(), 3600);

            // Return the card
            return $card->toArray();
        }

        /**
         * Get all the sets.
         * 
         * @return array The sets
         */
        public function getSets() : array {
            // Check if the sets exist in cache first
            $cachedSets = cache('sets');

            // If sets exist in cache, return them, otherwise get them from the API
            if ($cachedSets) {
                return $cachedSets;
            } else {
                // Get the sets
                $sets = Pokemon::Set()->all();
                
                // Cache the sets for an hour
                cache()->save('sets', $sets, 3600);

                // Return the sets
                return $sets;
            }
        }
    }
?>