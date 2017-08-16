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
        
        add_meta_box( 
            $this->data['id'], 
            $this->data['title'],
            array( $this, 'metabox_callback'),
            $this->data['post_type'],
            $this->data['context'],
            $this->data['priority'],
            $this->data['args']
       );
        
    }
  
    public function metabox_callback( $post, $box ) {

        switch( $box['args']['type'] ) {

            case 'text':
              $this->render_text( $box, $post->ID );
            break;

        }
    }
    
    public function render_text( $box, $post_id ) {
        
        wp_nonce_field( plugin_basename( __FILE__ ), 'meta_box_inhalt_nonce' );
        $value = get_post_meta( $post_id, $box['id'], true ); ?>
        <label for=<?php echo $box['id']; ?>></label>
        <input type=<?php echo $box['args']['type']; ?> id=<?php echo $box['id']; ?> name=<?php echo $box['id']; ?> size="60" value=<?php echo esc_attr( $value ); ?>><?php
        
    }
    
    public function save_meta_boxes( $post_id ) { 
        
        if( $_POST ) {
            
            if( isset($_POST[$this->data['id']]) ) { 
                update_post_meta( $post_id, $this->data['id'], $_POST[$this->data['id']] );
            }
            
        }
        
    }
    
}