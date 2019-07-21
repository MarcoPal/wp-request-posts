<?php

class WP_Request_Posts
{
    private $args = [];
    private $posts_per_page;
    private $count;

    function __construct($post_type = 'post', $page = 0, $posts_per_page = 10)
    {
        $this->posts_per_page         = $posts_per_page;
        $this->args['post_type']      = $post_type;
        $this->args['posts_per_page'] = $this->posts_per_page;
        $this->args['offset']         = $page * $posts_per_page;
    }

    public function add_tax($tax, $field = 'slug', $operator = 'IN')
    {
        if (empty($_REQUEST[$tax])) return $this;
        $this->args['tax_query'][] = [
            'taxonomy' => $tax,
            'field'    => $field,
            'terms'    => $_REQUEST[$tax],
            'operator' => $operator
        ];
        return $this;
    }

    public function add_meta($key, $compare = 'IN', $type = 'CHAR')
    {
        if (empty($_REQUEST[$key])) return $this;
        $this->args['meta_query'][] = [
            'key'     => $key,
            'value'   => $_REQUEST[$key],
            'compare' => $compare,
            'type'    => $type
        ];
        return $this;
    }

    public function add_meta_min_max($key, $type = 'NUMERIC')
    {

        $min_key = $key . "-min";
        $max_key = $key . "-max";

        if (empty($_REQUEST[$min_key]) && empty($_REQUEST[$max_key])) return $this;

        $has_min_only = !empty($_REQUEST[$min_key]) && empty($_REQUEST[$max_key]);
        $has_max_only = empty($_REQUEST[$min_key]) && !empty($_REQUEST[$max_key]);

        if ($has_min_only) {
            $value   = $_REQUEST[$min_key];
            $compare = '>=';
        } elseif ($has_max_only) {
            $value   = $_REQUEST[$max_key];
            $compare = '<=';
        } else {
            $value   = [$_REQUEST[$min_key], $_REQUEST[$max_key]];
            $compare = 'BETWEEN';
        }

        $this->args['meta_query'][] = [
            'key'     => $key,
            'value'   => $value,
            'compare' => $compare,
            'type'    => $type
        ];

        return $this;
    }

    public function debug()
    {
        if (function_exists('d')) {
            d($this->args);
        } else {
            echo '<pre>';
            var_dump($this->args);
            echo '</pre>';
        }
        return $this;
    }

    public function get_posts()
    {
        $posts = get_posts($this->args);
        $this->set_count();
        return $posts;
    }

    private function set_count()
    {
        $this->args['posts_per_page'] = -1;
        $this->args['offset']         = 0;
        $this->args['fields']         = 'ids';
        $this->count                  = count(get_posts($this->args));
    }

    public function count()
    {
        return $this->count;
    }

    public function last_page()
    {
        return floor($this->count / $this->posts_per_page);
    }
}