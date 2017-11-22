<?php

namespace RRZE\Remoter;

class Class_Create_Post_Type_Submenu_Page {
    
    public function __construct() {
        add_action( 'admin_menu', array(&$this, 'register_sub_menu'));
        add_action( 'admin_footer', array($this, 'remote_request_ajax'));
        add_action( 'wp_ajax_remote_request_action',  array($this,'remote_request_action'));
    }
 
    public function register_sub_menu() {
        add_submenu_page( 
           'edit.php?post_type=remote-server', __( 'API-Key Anfrage', 'rrze-remoter' ), __( 'API-Key Anfrage', 'rrze-remoter' ), 'manage_options', 'api_key_options', array(&$this, 'submenu_page_callback')
        );
    }
 
    public function submenu_page_callback() {
        global $blog_id;
        
        $current_user = wp_get_current_user();
        $current_blog_details = get_blog_details( array( 'blog_id' => $blog_id ) );
        
        $html =     '<div class="rrze-remoter-wrap">';
        $html .=    '<h2>' . __( 'API-Key Anfrage', 'rrze-remoter' ) . '</h2>';
        $html .=    '<h4>' . __( 'Hier können Sie einen API-Key beantragen.', 'rrze-remoter' ) .'</h4>';
        $html .=    '<p id="server-response"></p>';
        $html .=    '</div>';
        $html .=    '<form id="apikey_request_id" action="https://wordpress.dev/wp-admin/admin-ajax.php" method="post">';
        $html .=    '<input type="hidden" name="adminemail" value="' . $current_user->user_email . '"/>';
        $html .=    '<input type="hidden" name="domain" value="'. $current_blog_details->domain .'"/>';
        $html .=    '<p><label for="server_id">Server ID:<input type="text" name="server_id"/></label></p>';
        $html .=    '<button class="button button-primary" id="sbmBtn">API-Key anfordern</button>';
        $html .=    '</form>';
        
        echo $html;
    }
    
    
    public function remote_request_ajax() { ?>
	<script type="text/javascript" >
	
            jQuery(document).ready(function($) {
            $('#apikey_request_id').submit(function(event) {
        
                var serverdata = {
                    'adminemail'    : $('input[name=adminemail]').val(),
                    'domain'        : $('input[name=domain]').val(),
                    'serverid'      : $('input[name=server_id]').val(),
                };

                $.ajax({
                    url: ajaxurl,
                    data: {
                        'action':'remote_request_action',
                        'notices' : serverdata
                    },
                    success:function(data) {
                        $("#server-response").html(data);     
                    },
                    error: function(errorThrown){
                        console.log(errorThrown);
                    }
                });

                $('input[name=adminemail]').val(),
                $('input[name=domain]').val(),

                event.preventDefault();
            });
	 });
        </script> 
        <?php
    }

    
    public function remote_request_action() {
               
        $ip  = '131.188.12.134'; //$_SERVER['REMOTE_ADDR'];
        $adminemail = $_REQUEST['notices']['adminemail'];
        $domain     = $_REQUEST['notices']['domain'];
        $serverid   = $_REQUEST['notices']['serverid'];
        
        
        $domain_requested = query_posts('post_type=remote-server&p=' . $serverid);
        while (have_posts()): the_post(); ?>
        <?php 
        
        if (!empty($serverid)) {
        
        $meta = get_post_meta( get_the_ID(), 'domain' );
        
        }?>
            
        <?php endwhile;
        
        $response = wp_remote_get( 'http://remoter.dev/request.php?' .
            'ip=' . $ip .
            '&serverid=' . $serverid .    
            '&email=' . $adminemail . 
            '&domain=' . $domain . 
            '&requested_domain=' . (isset($meta[0]) ? $meta[0] : ''), 
            array( 'timeout' => 120, 'httpversion' => '1.1' )
        );
        
        echo $response['body'];

	wp_die();
    }
    
}