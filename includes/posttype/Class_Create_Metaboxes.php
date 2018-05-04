<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Create_Metaboxes {
    
    public function __construct() {
        $this->data = Class_Metaboxes_Data::Metaboxes_Data_Loader();
        add_action( 'add_meta_boxes', array( $this, 'adding_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 1 );
    }
    
    public function adding_meta_box() {
        global $post;
        
        if($post->post_type == 'remoter') {

            foreach ( $this->data as $box )  {

                add_meta_box( 
                    $box['id'], 
                    $box['title'],
                    array( $this, 'metabox_callback'),
                    $box['post_type'],
                    $box['context'],
                    $box['priority'],
                    $box['args']
                );

            }
        }

    }

    public function metabox_callback( $post, $box ) {

        switch( $box['args']['type'] ) {

            case 'text':
                $this->render_text( $box, $post->ID );
            break;
        
            case 'select':
                $this->render_select( $box, $post->ID );
            break;
        }

    }  

    public function render_text( $box, $post_id ) {

        wp_nonce_field( plugin_basename( __FILE__ ),
        'meta_box_inhalt_nonce' );
        $value = get_post_meta( $post_id, $box['id'], true ); ?>
        <label for=<?php echo $box['id']; ?>></label>
        <input type=<?php echo $box['args']['type']; ?> id=<?php echo $box['id']; ?> name=<?php echo $box['id']; ?> size="35" value=<?php echo esc_attr( $value ); ?>><?php
    }
    
    public function render_select( $box, $post_id ) {

        wp_nonce_field( plugin_basename( __FILE__ ),
        'meta_box_inhalt_nonce' );
        $value = get_post_meta( $post_id, $box['id'], true ); 
        $keys = array();
        $values = array();
        ?>

        <label for=<?php echo $box['id']; ?>></label>
        <select name=<?php echo $box['id'] ?> >
        <?php 

            for ($i = 0; $i < count($box['args']['elemente']); $i++) {
                $keys[]   = $box['args']['elemente'][$i]['value'];
                $values[]   = $box['args']['elemente'][$i]['value'];
            }

            $option_values = array_combine($keys, $values);

            foreach($option_values as $key => $value) {

                if($value == get_post_meta($post_id, $box['id'], true)) { ?>
                  <option value=<?php echo $value ?> selected><?php echo $value; ?></option>
                  <?php    
                } else { ?>
                  <option value=<?php echo $value ?> ><?php echo $value; ?></option>
                  <?php
                }
            }
        ?>
        </select><?php
      } 
    

    public function save_meta_boxes( $post_id ) { 
        
        $post  = get_post( $post_id );
        
        if ($post->post_type == 'remoter') {
            if( $_POST ) {
                foreach( $this->data as $box ) {
                    if( isset($_POST[$box['id']]) ) { 
                        update_post_meta( $post_id, $box['id'], $_POST[$box['id']] );
                    }
                } 
            }
        }
    }
}