# wp-request-posts
Retrieve Wordpress posts from POST or GET request

####Example of use

Starting from the url:  
https://yourwebsite.com/?color=blue&is_promo=1&price-min=50&price-max=100

```
<?php

$page = !empty($_GET['p']) ? $_GET['p'] : "0";

$query = new WP_Request_Posts('products', $page); 

$builder = $query
            ->add_tax('color')
            ->add_meta('is_promo', '==')
            ->add_meta_min_max('price');
            
            
// print the args array            
$builder->debug();

            
// get the posts            
$posts = $builder->get_posts();


// get the posts count
$count = $builder->count();


// get the last page count   
$last_page = $builder->last_page();



// params:
// WP_Request_Posts: post type, page num, posts per page (default 10)

// add_tax: taxonomy, field (default: 'slug'), operator (default: 'IN')
// add_meta: key, compare (default: 'IN'), type (default: 'CHAR')
// add_meta_min_max: key, type (default: 'NUMERIC')

// If the field or the key is non available in your request,  
// the param will be simply skipped

```



#####Note

In order to use the **add_meta_min_max()** method, 
you should add the suffixes "-min" and "-max" to your inputs name.

Example:

```
// Meta key = 'price'

<input type="number" name="price-min" />
<input type="number" name="price-max" />

```


