<?php
namespace mirocow\elasticsearch\components\queries\helpers;

class QueryHelper
{
    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-bool-query.html
     * @param array $filterQueries
     * @param array $mustQueries
     * @param array $shouldQueries
     * @param array $mustNotQueries
     * @return array
     */
    public static function bool($filterQueries = [], $mustQueries = [], $shouldQueries = [], $mustNotQueries = []) :array
    {
        $out = [];
        if (!empty($filterQueries)) {
            $out['bool']['filter'] = $filterQueries;
        }
        if (!empty($mustQueries)) {
            $out['bool']['must'] = $mustQueries;
        }
        if (!empty($shouldQueries)) {
            $out['bool']['should'] = $shouldQueries;
        }
        if (!empty($mustNotQueries)) {
            $out['bool']['must_not'] = $mustNotQueries;
        }
        return $out;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-bool-query.html#_scoring_with_literal_bool_filter_literal
     * @param $filterQueries
     * @return array
     */
    public static function filter($filterQueries) :array
    {
        return self::bool($filterQueries);
    }

    /**
     * @param $mustQueries
     * @return array
     */
    public static function must($mustQueries) :array
    {
        return self::bool([], $mustQueries);
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-bool-query.html
     * @param $shouldQueries
     * @return array
     */
    public static function should($shouldQueries) :array
    {
        return self::bool([], [], $shouldQueries);
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-bool-query.html
     * @param $mustNotQueries
     * @return array
     */
    public static function mustNot($mustNotQueries) :array
    {
        return self::bool([], [], [], $mustNotQueries);
    }

    /**
     * @param string $field
     * @param string[]|int[] $terms
     * @return array
     */
    public static function terms($field, $terms) :array
    {
        return [
            'terms' => [
                $field => $terms
            ]
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-term-query.html
     * @param string $field
     * @param string $term
     * @return array
     */
    public static function term($field, $term) :array
    {
        return [
            'term' => [
                $field => $term
            ]
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-range-query.html
     * @param string $field
     * @param string|int $gte greater than or equal
     * @param string|int $lte less than or equal
     * @param array $options options to pass into the range query
     * @return array
     */
    public static function range($field, $gte = '', $lte = '', $options = []) :array
    {
        if ($gte !== '') {
            $options['gte'] = $gte;
        }
        if ($lte !== '') {
            $options['lte'] = $lte;
        }
        return [
            'range' => [
                $field => $options
            ]
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-nested-query.html
     * @param string $path
     * @param string $query
     * @return array
     */
    public static function nest($path, $query = '') :array
    {
        return [
            'nested' => [
                'path' => $path,
                'query' => self::query($query)
            ]
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-wildcard-query.html
     * @param string $field
     * @param string $searchQuery
     * @return array
     */
    public static function fullWildcard($field, $searchQuery) :array
    {
        return [
            'wildcard' => [
                $field => "*$searchQuery*"
            ]
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-wildcard-query.html
     * expects * somewhere in the string, if at the end, might as well just use prefix instead
     * @param string $field
     * @param string $searchQuery
     * @return array
     */
    public static function wildcard($field, $searchQuery) :array
    {
        return [
            'wildcard' => [
                $field => "$searchQuery"
            ]
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/search-suggesters.html#global-suggest
     * @param string $field
     * @param string $searchQuery
     * @return array
     */
    public static function suggest($field, $searchQuery) :array
    {
        $prefix = self::prefix($field, $searchQuery);
        return [
            'suggest' => $prefix
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-prefix-query.html
     * @param string $field
     * @param string|array $searchTerms
     * @return array
     */
    public static function prefix($field, $searchTerms) :array
    {
        return [
            'prefix' => [
                $field => $searchTerms
            ]
        ];
    }

    /**
     * @param array $fields
     * @param string $query
     * @param string $type
     * @param int $max_expansions
     * @return array
     */
    public static function multiMatch($fields, $query, $type, $max_expansions) :array
    {
        return [
            'multi_match' => [
                'query' => $query,
                'fields' => $fields,
                'type' => $type,
                'max_expansions' => $max_expansions,
            ],
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-match-query.html
     * @param $field
     * @param $match
     * @param string $type
     * @return array
     */
    public static function match($field, $match, $type = 'match') :array
    {
        return [
            $type => [
                $field => $match,
            ]
        ];
    }

    /**
     * @param $field
     * @return array
     */
    public static function exists($field) :array
    {
        return [
            'exists' => [
                'field' => $field,
            ],
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-match-all-query.html
     * @param string $query
     * @return string
     */
    public static function query($query = '')
    {
        return empty($query) ? ["match_all" => (object) []] : $query;
    }

    /**
     * https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-query-string-query.html
     * @param string $query
     * @param string $default_field
     * @return array
     */
    public static function query_string($query = '', $default_field = '_all') :array
    {
        return [
            'query_string' => [
                'default_field' => $default_field,
                'query' => $query,
            ]
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/search-request-from-size.html
     * @param integer|null $limit
     * @return array
     */
    public static function limit($limit = null) :array
    {
        if(!$limit){
            return [];
        }

        return [
            'size' => (int) $limit
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/5.6/search-request-from-size.html
     * @param integer|null $offset
     * @return array
     */
    public static function offset($offset = null) :array
    {
        if(!$offset){
            return [];
        }

        return [
            'from' => (int) $offset
        ];
    }

    /**
     * https://www.elastic.co/guide/en/elasticsearch/reference/5.6/search-request-sort.html
     * @param $columns
     *
     * @return array
     */
    public static function sortBy($columns) :array
    {
        if(!$columns){
            return [];
        }

        return self::buildOrderBy($columns);
    }

    /**
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-sort.html#_sort_mode_option
     * @param string $column
     * @param int $direction
     * @param string $mode
     * @return array|object
     */
    public static function sortByMode(string $column, int $direction = SORT_ASC, $mode = 'sum')
    {
        if(!$column){
            return [];
        }

        return (object) [
            $column => (object) [
                'order' => $direction === SORT_DESC ? 'desc' : 'asc',
                'mode' => $mode,
            ],
        ];
    }

    /**
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-sort.html#_sort_mode_option
     * @param string $column
     * @param int $direction
     * @param string $mode
     * @return array|object
     */
    public static function sortByCount(string $column, int $direction = SORT_ASC, $mode = 'sum')
    {
        return self::sortByMode($column, $direction, 'sum');
    }

    /**
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-sort.html#_script_based_sorting
     *
     * @example doc['column'].values.size()
     * @param string $column
     * @param int $direction
     * @return array
     */
    public static function sortByScript($script = '', int $direction = SORT_ASC)
    {
        if(!$script){
            return [];
        }

        return (object) [
            '_script' => [
                'script' => $script,
                'type' => 'number',
                'order' => $direction === SORT_DESC ? 'desc' : 'asc',
                'lang' => 'painless',
            ]
        ];
    }

    /**
     * Adds order by condition to the query
     * @param $columns Examples: ['field' => SORT_ASC]; ['field' => ["order" => "asc", "mode" => "avg"]]
     * @return array
     */
    private static function buildOrderBy($columns) :array
    {
        if (empty($columns)) {
            return [];
        }
        $orders = [];
        foreach ($columns as $name => $direction) {
            if (is_string($direction)) {
                $column = $direction;
                $direction = SORT_ASC;
            } else {
                $column = $name;
            }
            if ($column == '_id') {
                $column = '_uid';
            }

            // allow elasticsearch extended syntax as described in http://www.elastic.co/guide/en/elasticsearch/guide/master/_sorting.html
            if (is_array($direction)) {
                $orders[] = (object) [$column => $direction];
            } else {
                $orders[] = (object) [$column => ($direction === SORT_DESC ? 'desc' : 'asc')];
            }
        }

        return $orders;
    }

    /**
     * https://www.elastic.co/guide/en/elasticsearch/reference/5.6/query-dsl-script-query.html
     * https://www.elastic.co/guide/en/elasticsearch/painless/5.6/painless-specification.html
     * @param string $script
     * @param array $params
     * @return array
     */
    public static function queryByScript(string $script, $params = [])
    {
        return [
            'script' => (object) [
                'script' => (object) [
                    'source' => $script,
                    'lang' => 'painless',
                    'params' => (object) $params,
                ]
            ],
        ];
    }
}
