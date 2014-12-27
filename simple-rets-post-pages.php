<?php

/*
 *
 * simple-rets-post-pages.php - Copyright (C) Reichert Brothers 2014
 * This file provides the logic for the simple-rets custom post type pages.
 *
*/

/* Code starts here */
add_action( 'init', array( 'simpleRetsCustomPostPages', 'simpleRetsPostType' ) );

add_filter( 'single_template', array( 'simpleRetsCustomPostPages', 'loadSimpleRetsPostTemplate' ) );

add_filter( 'the_content', array( 'simpleRetsCustomPostPages', 'simpleRetsDefaultContent' ) );

add_action( 'add_meta_boxes', array( 'simpleRetsCustomPostPages', 'postFilterMetaBox' ) );
add_action( 'add_meta_boxes', array( 'simpleRetsCustomPostPages', 'postTemplateMetaBox' ) );


add_action( 'save_post', array( 'simpleRetsCustomPostPages', 'postFilterMetaBoxSave' ) );
add_action( 'save_post', array( 'simpleRetsCustomPostPages', 'postTemplateMetaBoxSave' ) );


add_action( 'admin_enqueue_scripts', array( 'simpleRetsCustomPostPages', 'postFilterMetaBoxJs' ) );
add_action( 'admin_init', array( 'simpleRetsCustomPostPages', 'postFilterMetaBoxCss' ) );
// ^TODO: load css/js only on retsd-listings post type pages when admin


class simpleRetsCustomPostPages {

    // Custom Post Type
    public static function simpleRetsPostType() {
        $labels = array(
            'name'          => __( 'Rets Pages' ),
            'singular_name' => __( 'Rets Page' ),
            'add_new_item'  => __( 'New Rets Page' ),
            'edit_item'     => __( 'Edit Rets Page' ),
            'new_item'      => __( 'New Rets Page' ),
            'view_item'     => __( 'View Rets Page' ),
            'all_items'     => __( 'All Rets Pages' ),
            'search_items'  => __( 'Search Rets Pages' ),
        );
        $args = array(
            'public'          => true,
            'has_archive'     => false,
            'labels'          => $labels,
            'description'     => 'SimplyRets property listings pages',
            'query_var'       => true,
            'menu_positions'  => '15',
            'capability_type' => 'page',
            'hierarchical'    => true,
            'taxonomies'      => array(),
            'supports'        => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
            'rewrite'         => true
        );
        register_post_type( 'retsd-listings', $args );
    }

    public static function postFilterMetaBox() {
        add_meta_box(
            'sr-meta-box-filter'
            , __( 'Filter Results on This Page', 'sr-textdomain')
            , array('simpleRetsCustomPostPages', 'postFilterMetaBoxMarkup')
            , 'retsd-listings'
            , 'normal'
            , 'high'
        );
    }

    public static function postTemplateMetaBox() {
        add_meta_box(
             'sr-template-meta-box'
             , __('Page Template', 'sr-textdomain')
             , array( 'simpleRetsCustomPostPages', 'postTemplateMetaBoxMarkup' )
             , 'retsd-listings'
             , 'side'
             , 'core'
        );
    }

    public static function postFilterMetaBoxJs() {
        wp_register_script( 'simple-rets-admin-js', plugins_url( '/rets/js/simple-rets-admin.js' ), array( 'jquery' ) );
        wp_enqueue_script( 'simple-rets-admin-js' );
    }

    public static function postFilterMetaBoxCss() {
        wp_register_style( 'simple-rets-admin-css', plugins_url( '/rets/css/simple-rets-admin.css' ) );
        wp_enqueue_style( 'simple-rets-admin-css' );

    }

    public static function postFilterMetaBoxMarkup( $post ) {
        wp_nonce_field( basename(__FILE__), 'sr_meta_box_nonce' );
        $min_price_filter = "";
        $max_price_filter = "";
        $min_bed_filter   = "";
        $max_bed_filter   = "";
        $agent_id_filter  = "";

        $sr_filters = get_post_meta( $post->ID, 'sr_filters', true);

        ?>
        <div class="current-filters">
            <span class="filter-add">
              <?php _e( 'Add new Filter' ); ?>
            </span>
            <select name="sr-filter-select" id="sr-filter-select">
                <option> -- Select a Filter -- </option>
                <option val="minPrice-option"> Minimum Price  </option>
                <option val="maxPrice-option"> Maximum Price  </option>
                <option val="minBed-option">   Minimum Beds   </option>
                <option val="maxBed-option">   Maximum Beds   </option>
                <option val="agentId-option">  Listing Agent  </option>
            </select>
            <hr>
        </div>

        <div class="sr-meta-inner">

          <div class="sr-filter-input" id="sr-min-price-span">
            <label for="sr-min-price-input">
              <?php _e( 'Minimum Price', 'sr-textdomain' ) ?>
            </label>
            <input id="minPrice" type="text" name="sr_filters[minPrice]"
              value="<?php print_r( $min_price_filter ); ?>"/>
            <span class="sr-remove-filter">Remove Filter</span>
          </div>

          <div class="sr-filter-input" id="sr-max-price-span">
            <label for="sr-max-price-input">
              Maximum Price:
            </label>
            <input id="maxPrice" type="text" name="sr_filters[maxPrice]"
              value="<?php print_r( $max_price_filter ); ?>"/>
            <span class="sr-remove-filter">Remove Filter</span>
          </div>

          <div class="sr-filter-input" id="sr-min-bed-span">
            <label for="sr-min-bed-input">
              Minimum Bedrooms:
            </label>
            <input id="minBed" type="text" name="sr_filters[minBed]"
              value="<?php print_r( $min_bed_filter ); ?>"/>
            <span class="sr-remove-filter">Remove Filter</span>
          </div>

          <div class="sr-filter-input" id="sr-max-bed-span">
            <label for="sr-max-bed-input">
              Maximum Bedrooms:
            </label>
            <input id="maxBed" type="text" name="sr_filters[maxBed]"
              value="<?php print_r( $max_bed_filter ); ?>"/>
            <span class="sr-remove-filter">Remove Filter</span>
          </div>

          <div class="sr-filter-input" id="sr-listing-agent-span">
            <label for="sr-listing-agent-input">
              Listing Agent MLS Id:
            </label>
            <input id="agentId" type="text" name="sr_filters[agentId]"
              value="<?php print_r( $agent_id_filter ); ?>"/>
            <span class="sr-remove-filter">Remove Filter</span>
          </div>

          <span id="filter-here"></span>

        </div>
        <?php

        echo '<hr>Current filters: <br>'; print_r( $sr_filters );
        echo '<br>';
        // ^TODO: Remove degbug

        // on page load, if there are any filters already saved, load them,
        // show the input field, and remove the option from the dropdown
        foreach( $sr_filters as $key=>$val ) {
            if ( $val != '' ) {
                ?>
                <script>
                    var filterArea = jQuery('.current-filters');
                    var key = jQuery(<?php print_r( $key ); ?>);
                    var val = <?php echo json_encode( $val ); ?>;
                    var parent = key.parent();

                    key.val(val); // set value to $key
                    console.log(key.val());
                    filterArea.append(parent); //append div to filters area
                    parent.show(); //display: block the div since it has a value

                </script>
                <?php
            }
        };
    }

    public static function postFilterMetaBoxSave( $post_id ) {
        $current_nonce = $_POST['sr_meta_box_nonce'];
        $is_autosaving = wp_is_post_autosave( $post_id );
        $is_revision   = wp_is_post_revision( $post_id );
        $valid_nonce   = ( isset( $current_nonce ) && wp_verify_nonce( $current_nonce, basename( __FILE__ ) ) ) ? 'true' : 'false';

        if ( $is_autosaving || $is_revision || !$valid_nonce ) {
            return;
        }

        $sr_filters = $_POST['sr_filters'];
        update_post_meta( $post_id, 'sr_filters', $sr_filters );
    }

    public static function postTemplateMetaBoxMarkup( $post ) {
        wp_nonce_field( basename(__FILE__), 'sr_template_meta_nonce' );

        $current_template = get_post_meta( $post->ID, 'sr_page_template', true);
        $template_options = get_page_templates();

        $box_label = '<label class="sr-filter-meta-box" for="sr_page_template">Page Template</label>';
        $box_select = '<select name="sr_page_template" id="sr-page-template-select">';
        $box_option = '';
        $box_default_option = '<option value="">Default Template</option>';

        echo $box_label;

        foreach (  $template_options as $name=>$file ) {
            if ( $current_template == $file ) {
                $box_option .= '<option value="' . $file . '" selected="selected">' . $name . '</option>';
            } else {
                $box_option .= '<option value="' . $file . '">' . $name . '</option>';
            }
        }

        echo $box_select;
        echo $box_default_option;
        echo $box_option;
        echo '</select>';
    }

    public static function postTemplateMetaBoxSave( $post_id ) {
        $current_nonce = $_POST['sr_template_meta_nonce'];
        $is_autosaving = wp_is_post_autosave( $post_id );
        $is_revision   = wp_is_post_revision( $post_id );
        $valid_nonce   = ( isset( $current_nonce ) && wp_verify_nonce( $current_nonce, basename( __FILE__ ) ) ) ? 'true' : 'false';

        if ( $is_autosaving || $is_revision || !$valid_nonce ) {
            return;
        }

        $sr_page_template = $_POST['sr_page_template'];
        update_post_meta( $post_id, 'sr_page_template', $sr_page_template );
    }

    public static function loadSimpleRetsPostTemplate() {
        $query_object = get_queried_object();
        $sr_post_type = 'retsd-listings';
        $page_template = get_post_meta( $query_object->ID, 'sr_page_template', true );

        $default_templates    = array();
        $default_templates[]  = 'single-{$object->post_type}-{$object->post_name}.php';
        $default_templates[]  = 'single-{$object->post_type}.php';
        $default_templates[]  = 'single.php';

        // only apply our template to our CPT pages
        if ( $query_object->post_type == $sr_post_type ) {
            if ( !empty( $page_template ) ) {
                echo 'need to load ' .$page_template;
                $default_templates = $page_template;
            }
        }

        $new_template = locate_template( $default_templates, false );
        return $new_template;
    }

    public static function simpleRetsDefaultContent( $content, $post ) {
        require_once( plugin_dir_path(__FILE__) . 'simple-rets-api-helper.php' );

        $post_type = get_post_type();
        $sr_post_type = 'retsd-listings';
        $br = '<br>';

        // only add listings for our CPT
        if ( $post_type == $sr_post_type ) {

            $query_object = get_queried_object();
            $listing_params = get_post_meta( $query_object->ID, 'sr_filters', true );

            if ( empty($listing_params) ) {
                return 'no filter params' . $content;
            }

            foreach ( $listing_params as $key=>$value ) {
                $content = 'param: ' . $key . ' value: ' . $value . $br . $content;
            }

            $content = simpleRetsApiHelper::retrieveRetsListings( $listing_params ) . $br . $content;

            return $content;
        }
    } // ^TODO: content needs to be appended, not prepended.

}
?>