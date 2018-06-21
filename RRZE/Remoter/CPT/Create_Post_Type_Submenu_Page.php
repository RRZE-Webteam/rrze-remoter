<?php

namespace RRZE\Remoter\CPT;

defined('ABSPATH') || exit;

class Create_Post_Type_Submenu_Page {

    public function __construct() {
        add_action('admin_menu', array(&$this, 'register_sub_menu'));
        add_action('admin_footer', array($this, 'remote_request_ajax'));
        add_action('wp_ajax_remote_request_action', array($this, 'remote_request_action'));
    }

    public function register_sub_menu() {
        add_submenu_page(
                'edit.php?post_type=remoter', __('Request for an API key', 'rrze-remoter'), __('Request for an API key', 'rrze-remoter'), 'manage_options', 'api_key_options', array(&$this, 'submenu_page_callback')
        );
    }

    public function submenu_page_callback() {
        global $blog_id;

        $current_user = wp_get_current_user();
        $current_blog_details = get_blog_details(array('blog_id' => $blog_id));

        $html = '<div class="rrze-remoter-wrap">';
        $html .= '<h2>' . __('API Key Request', 'rrze-remoter') . '</h2>';
        $html .= '<h4>' . __('You can apply for an API key here.', 'rrze-remoter') . '</h4>';
        $html .= '<p id="server-response"></p>';
        $html .= '</div>';
        $html .= '<form id="apikey_request_id">';
        $html .= '<input type="hidden" name="adminemail" value="' . $current_user->user_email . '"/>';
        $html .= '<input type="hidden" name="domain" value="' . $current_blog_details->domain . '"/>';
        $html .= '<p><label for="server_id">' . __('Server ID:', 'rrze-remoter') . '<input type="text" name="server_id"/></label></p>';
        $html .= '<button class="button button-primary" id="sbmBtn">' . __('Request API key', 'rrze-remoter') . '</button>';
        $html .= '</form>';

        echo $html;
    }

    public function remote_request_ajax() {
        ?>
        <script type="text/javascript" >

            jQuery(document).ready(function ($) {
                $('#apikey_request_id').submit(function (event) {

                    var serverdata = {
                        'adminemail': $('input[name=adminemail]').val(),
                        'domain': $('input[name=domain]').val(),
                        'serverid': $('input[name=server_id]').val(),
                    };

                    $.ajax({
                        type: 'post',
                        url: ajaxurl,
                        data: {
                            'action': 'remote_request_action',
                            'notices': serverdata
                        },
                        success: function (data) {
                            $("#server-response").html(data);
                        },
                        error: function (errorThrown) {
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
        $adminemail = $_REQUEST['notices']['adminemail'];
        $domain = $_REQUEST['notices']['domain'];
        $serverid = $_REQUEST['notices']['serverid'];

        $meta = 0;
        $html = '';

        $sslverify = defined('WP_DEBUG') && WP_DEBUG ? false : true;

        query_posts('post_type=remoter&p=' . $serverid);
        while (have_posts()): the_post();
            $meta = get_post_meta(get_the_ID(), 'domain');
        endwhile;

        $responseRequest = wp_remote_get('https://' . $meta[0] . '/request.php', ['sslverify' => $sslverify]);
        $status_code = wp_remote_retrieve_response_code($responseRequest);

        if (!$meta) {

            $html = __('The server ID is not assigned!', 'rrze-remoter');
        } elseif (empty($serverid)) {

            $html = __('Please enter a server ID!', 'rrze-remoter');
        } elseif (!is_numeric($serverid)) {

            $html = __('Only numeric values are allowed!', 'rrze-remoter');
        } elseif (200 == $status_code) {

            $response = wp_remote_get('https://' . $meta[0] . '/request.php?' .
                    '&serverid=' . $serverid .
                    '&email=' . $adminemail .
                    '&domain=' . $domain .
                    '&requested_domain=' . (isset($meta[0]) ? $meta[0] : ''), array('timeout' => 120, 'httpversion' => '1.1', 'sslverify' => $sslverify)
            );

            echo $response['body'];
        } else {
            $html = '<h4>' . __('The necessary files are not yet or in the wrong place on the server!', 'rrze-remoter') . '</h4>';
            $html .= '<p>' . __('Please put the following files on your server first:', 'rrze-remoter') . '</p>';
            $html .= '<ul>';
            $html .= '<li>request.php</li>';
            $html .= '<li>readdirectories.php</li>';
            $html .= '<li>data.csv</li>';
            $html .= '<li>AccessControl/';
            $html .= '</ul>';
            $html .= '<p>' . sprintf('You can find them in the Gitlab Respository:', 'rrze-remoter') . ' <a href="https://gitlab.rrze.fau.de/rrze-webteam/rrze-remoter-server-files">' . __('Link', 'rrze-remoter') . '</a></p>';
            $html .= '</div>';
        }

        echo $html;

        wp_die();
    }

}
