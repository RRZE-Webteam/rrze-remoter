<?php

/*
/* https://galaxyinternet.us/php-pagination-array-chunk-pagination/*/
echo '<h3>Tabellenansicht</h3>';

echo '<pre>';
print_r($this->remote_data);
echo '</pre>';

$table = '<table>';

$id = uniqid();

foreach ($this->remote_data as $key => $value) {

    $table .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host'] . '/' . $file_index . (($recursiv == 1) ? '' : '/') . $value . '">' . basename($value) . '</a></td></tr>';

}

$table .= '</table>';
echo $table;
 /*
 
function example_ajax_request() {
  
    // The $_REQUEST contains all the data sent via AJAX from the Javascript call
    if ( isset($_REQUEST) ) {
        
        print_r($_REQUEST);
      
        $fruit = $_REQUEST['fruit'];
          
        // This bit is going to process our fruit variable into an Apple
        if ( $fruit == 'Banana' ) {
            $fruit = 'Apple';
        }
      
        // Now let's return the result to the Javascript function (The Callback) 
        echo $fruit;        
    }
      
    // Always die in functions echoing AJAX content
   die();
   
//add_action( 'wp_ajax_example_ajax_request', 'example_ajax_request' );
//add_action( 'wp_ajax_nopriv_example_ajax_request', 'example_ajax_request' );
}*/


/*
namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Remoter_Table_View {
    
    public function __construct() {
        add_action( 'wp_ajax_example_ajax_request', array($this, 'example_ajax_request' ));
        add_action( 'wp_ajax_nopriv_example_ajax_request', array($this, 'example_ajax_request' ));
    }
    
    public function rrze_remoter_script() { ?>
        <script>
        jQuery(document).ready(function($) {

            // This is the variable we are passing via AJAX
            var fruit = 'Banana';

            // This does the ajax request (The Call).
            $.ajax({
                url: frontendajax.ajaxurl, // Since WP 2.8 ajaxurl is always defined and points to admin-ajax.php
                data: {
                    'action':'example_ajax_request', // This is a our PHP function below
                    'fruit' : fruit // This is the variable we are sending via AJAX
                },
                success:function(data) {
            // This outputs the result of the ajax request (The Callback)
                    window.alert(data);
                },  
                error: function(errorThrown){
                    window.alert(errorThrown);
                }
            });   

        });
        </script><?php
    }
    
    public function example_ajax_request() {
  
        // The $_REQUEST contains all the data sent via AJAX from the Javascript call
        if ( isset($_REQUEST) ) {

            print_r($_REQUEST);

            //echo $this->remote_server_shortcode;

            $fruit = $_REQUEST['fruit'];

            // This bit is going to process our fruit variable into an Apple
            if ( $fruit == 'Banana' ) {
                $fruit = 'Apple';
            }

            // Now let's return the result to the Javascript function (The Callback) 
            echo $fruit;        
        }

        // Always die in functions echoing AJAX content
       die();
   
    }
}
 
 */