<?php
/**
 * Plugin Name: calisia-hide-category
 * Author: Tomasz Boroń
 */

 //pass an array of category slugs that you want to exclude
$calisia_hide_category = new calisia_hide_category(
    array(
        'warzywa-gotowane'
    )
);


class calisia_hide_category{
    private $category_slugs;

    function __construct($slugs){
        $this->category_slugs = $slugs;
        add_filter( 'get_terms', array($this,'exclude_category'), 10, 3 );
        add_filter( 'woocommerce_product_categories_widget_args', array($this,'exclude_widget_category') );
        add_action( 'woocommerce_product_query', array($this,'exclude_from_shop_page') );  
    }

    //hide on shop page (if categories are being displayed)
    public function exclude_category( $terms, $taxonomies, $args ) {
        $new_terms = array();
        if ( in_array( 'product_cat', $taxonomies ) /*&& !is_admin()*/ && is_page() ) {
            foreach ( $terms as $key => $term ) {
                if ( ! in_array( $term->slug, $this->category_slugs ) ) {
                    $new_terms[] = $term;
                }
            }
            $terms = $new_terms;
        }
        return $terms;
    }

    //Exclude category from category widget
    public function exclude_widget_category( $args ) {
        $term_ids = array();
        foreach($this->category_slugs as $slug){
            $term_ids[] = get_term_by( 'slug', $slug, 'product_cat' )->term_id;
        }
        $args['exclude'] = $term_ids;
        
        return $args;
    }

    //Exclude products from a particular category on the shop page
    function exclude_from_shop_page( $q ) {
        $tax_query = (array) $q->get( 'tax_query' );
        $tax_query[] = array(
               'taxonomy' => 'product_cat',
               'field' => 'slug',
               'terms' => $this->category_slugs,
               'operator' => 'NOT IN'
        );
        $q->set( 'tax_query', $tax_query );
    
    }
}


