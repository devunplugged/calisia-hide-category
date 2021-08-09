<?php
/**
 * Plugin Name: calisia-hide-category
 * Author: Tomasz Boroń
 * Text Domain: calisia-hide-category
 * Domain Path: /languages
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

define('CALISIA_HIDE_CATEGORY_ROOT', __DIR__);

require_once CALISIA_HIDE_CATEGORY_ROOT . '/src/renderer/interfaces/irenderer.php';
require_once CALISIA_HIDE_CATEGORY_ROOT . '/src/renderer/renderer.php';

require_once CALISIA_HIDE_CATEGORY_ROOT . '/src/inputs/factories/input-factory.php';
require_once CALISIA_HIDE_CATEGORY_ROOT . '/src/inputs/input.php';

require_once CALISIA_HIDE_CATEGORY_ROOT . '/src/settings/field.php';
//require_once CALISIA_HIDE_CATEGORY_ROOT . '/src/settings/menu-page.php';
require_once CALISIA_HIDE_CATEGORY_ROOT . '/src/settings/options.php';
require_once CALISIA_HIDE_CATEGORY_ROOT . '/src/settings/section.php';
require_once CALISIA_HIDE_CATEGORY_ROOT . '/src/settings/settings-page.php';
//require_once CALISIA_HIDE_CATEGORY_ROOT . '/src/settings/submenu-page.php';


$calisia_hide_category = new calisia_hide_category();


class calisia_hide_category{
    private $category_slugs;

    function __construct(){
        $this->load_categories_from_settings();

        //load plugin textdomain
        add_action( 'init', [$this, 'load_textdomain'] );

        //settings api
        add_action( 'admin_menu', [$this, 'AddSettingsPage'] );
        add_action( 'admin_init', [$this, 'RegisterSettings'] );

        //category hiding
        add_filter( 'get_terms', [$this,'exclude_category'], 10, 3 );
        add_filter( 'woocommerce_product_categories_widget_args', [$this,'exclude_widget_category'] );
        add_action( 'woocommerce_product_query', [$this,'exclude_from_shop_page'] );  
        add_filter( 'wp_nav_menu_objects', [$this,'filter_menu'], 10, 2 );
    }

    public function load_categories_from_settings(){
        $options = new calisia_hide_category\settings\Options('hidden-categories-option-name');
        $this->category_slugs = array_map('trim', explode(',', $options->get_option_value('hidden-categories')));
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'calisia-hide-category', false, 'calisia-hide-category/languages' );
    }

    public function AddSettingsPage(){
        $menuPage = new calisia_hide_category\settings\SettingsPage(new calisia_hide_category\renderer\DefaultRenderer());
        $menuPage->pageTitle = __( 'Category Hiding', 'calisia-hide-category' );
        $menuPage->menuTitle = __( 'Hide Category', 'calisia-hide-category' );
        $menuPage->capability = 'manage_options';
        $menuPage->menuSlug = 'calisia-hide-category';
        $menuPage->pageTemplate = 'default-settings-page';
        //$menuPage->position = 4;
        $menuPage->optionGroup = 'calisia-hide-category-option-group';
        $menuPage->page = 'calisia-hide-category-settings-page';
        $menuPage->Add();

    }

    public function RegisterSettings(){

        $section = new calisia_hide_category\settings\Section(new calisia_hide_category\renderer\DefaultRenderer());
        $section->id = 'calisia-hide-category-section-id';
        $section->title = __( 'Category Visibility', 'calisia-hide-category' );
        $section->template = 'section-text-example-2';
        $section->page = 'calisia-hide-category-settings-page';
        $section->text = __( 'Choose which categories to hide', 'calisia-hide-category' );
        $section->Add();

        $options = new calisia_hide_category\settings\Options('hidden-categories-option-name');
        $options->RegisterSetting('calisia-hide-category-option-group');

        $field = new calisia_hide_category\settings\Field(new calisia_hide_category\renderer\DefaultRenderer());     
        $field->id = 'hidden-categories';
        $field->title = __( 'Hidden Categories', 'calisia-hide-category' );
        $field->page = 'calisia-hide-category-settings-page';
        $field->sectionId = 'calisia-hide-category-section-id';
        $field->optionName = 'hidden-categories-option-name';   
        $field->input = calisia_hide_category\inputs\factories\InputFactory::Create('input');
        $field->input->value = $options->get_option_value($field->id);
        $field->input->label = __( 'Write slug of category you wish to exclude. Separate multiple with comma ","', 'calisia-hide-category' );
        $field->Add();

    }

    //hide on shop page (if categories are being displayed)
    public function exclude_category( $terms, $taxonomies, $args ) {
        $new_terms = [];
        if ( in_array( 'product_cat', $taxonomies ) /*&& !is_admin()*/ && is_page() ) {
            foreach ( $terms as $key => $term ) {
                if ( !in_array( $term->slug, $this->category_slugs ) ) {
                    $new_terms[] = $term;
                }
            }
            $terms = $new_terms;
        }
        return $terms;
    }

    //Exclude category from category widget
    public function exclude_widget_category( $args ) {
        $term_ids = [];
        foreach($this->category_slugs as $slug){
            $term = get_term_by( 'slug', $slug, 'product_cat' );
            if($term != false)
                $term_ids[] = $term->term_id;
        }
        $args['exclude'] = $term_ids;
        
        return $args;
    }

    //Exclude products from a particular category on the shop page
    public function exclude_from_shop_page( $q ) {
        $tax_query = (array) $q->get( 'tax_query' );
        $tax_query[] = [
               'taxonomy' => 'product_cat',
               'field' => 'slug',
               'terms' => $this->category_slugs,
               'operator' => 'NOT IN'
        ];
        $q->set( 'tax_query', $tax_query );
    
    }

    //hide categories from nav menu
    public function filter_menu( $objects, $args ) {
        $links = [];
        foreach($this->category_slugs as $slug){
            $links[] = get_term_link( $slug, 'product_cat' );
        }
        foreach ( $objects as $key => $object ){
            if(in_array($object->url, $links) === true){
                unset($objects[$key]);
            }
        }   
        return $objects;
    
    }
}


