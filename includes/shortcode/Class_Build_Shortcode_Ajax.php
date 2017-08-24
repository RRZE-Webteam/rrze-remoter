<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Build_Shortcode_Ajax {
    
    public function __construct() {
      
        add_action( 'wp_ajax_rrze_remote_table_ajax_request', array($this, 'rrze_remote_table_ajax_request' ));
        add_action( 'wp_ajax_nopriv_rrze_remote_table_ajax_request', array($this, 'rrze_remote_table_ajax_request' ));
        add_shortcode('remoter-ajax', array($this, 'shortcode')); 
        add_action( 'wp_footer', array($this,'rrze_remote_table_script_footer'));
        
    }
    
    public function shortcode($atts) {
        
        $this->remote_server_shortcode = shortcode_atts( array(
            'server_id' => '2212879',
            'file'      => 'http://remoter.dev/images/jeny.png',
            'index'     => 'images',
            'recursiv'  => '1',
            'max'       => '5',
            'filetype'  => 'jpg',
            'view'      => 'list',
        ), $atts );
        
        return $this->query_args($this->remote_server_shortcode);
       
    }
    
    public function query_args($args) {
        
        $this->remote_server_args = array(
            'post_type'         =>  'Remote-Server',
            'p'                 =>  $args['server_id'],
            'posts_per_page'    =>  1,
            'orderby'           =>  'date',
            'order'             =>  'DESC'
        );
        
        return $this->show_results_as_list($this->remote_server_args);
    }
    
    public function show_results_as_list($query_arguments) {
        
        global $post;
        
        $shortcode_values = array();
        
        $the_query = new \WP_Query( $query_arguments);
        
        if ( $the_query->have_posts() ) {
            
            while ( $the_query->have_posts() ) {
                $the_query->the_post();

                $url = get_post_meta($post->ID, 'url', true); 
                
                echo $url;

                $file_index = $this->remote_server_shortcode['index'];
                $view = $this->remote_server_shortcode['view'];
                $recursiv = $this->remote_server_shortcode['recursiv'];
                $filetype = $this->remote_server_shortcode['filetype'];
                $this->remote_data = Class_Grab_Remote_Files::get_files_from_remote_server($this->remote_server_shortcode, $url);

                $url = parse_url(get_post_meta($post->ID, 'url', true)); 

                $number_of_chunks = 4;

                $data = array_chunk($this->remote_data, $number_of_chunks);
                
                $pagecount = count($data);
                
                echo '<h3>Tabellenansicht mit Pagination</h3>';
                
                $i = 0;

                $id = uniqid();
                
                $output = '<div id="result"><table>';
                
                foreach ($data[$i] as $key => $value) {

                    $output .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host'] . '/' . $file_index . (($recursiv == 1) ? '' : '/') . $value . '">' . basename($value) . '</a></td></tr>';

                }
                
                $output .= '</table></div>';
                
                echo $output;
               
                $html = '<nav class="pagination pagebreaks" role="navigation"><h3>Seite:</h3><span class="subpages">';

                for ($i = 1; $i <= $pagecount; $i++) {
                    
                    $html .='<a data-filetype="' . $filetype . '" data-recursiv="' . $recursiv . '" data-index="' . $file_index . '" data-host="' . $url['host'] . '" data-chunk="' . $number_of_chunks . '" data-pagecount-value= "' . $pagecount . '" class="page-'. $i.'" href="#sign_up"><span class="'. ($i==1 ? 'number active' : 'number') .'">'.$i.'</span></a>';
                   
                }
                
                $html .= '</span></nav>';
                echo $html;

            }
         
            wp_reset_postdata();
        } else {
                echo 'no posts found';
        }
    }
    
    public function rrze_remote_table_script_footer(){ 
        
        $arr = $this->remote_data;
        
        ?>
  
        <script>
        jQuery(document).ready(function($) {

            var arr = <?php echo json_encode($arr); ?>;

            $('a[href="#sign_up"]').click(function(){
                var link = $(this).attr('class');
                var page = link.replace('page-', '');
                var pagecount = $(this).attr('data-pagecount-value');
                var chunk = $(this).attr('data-chunk');
                var host = $(this).attr('data-host');
                var index = $(this).attr('data-index');
                var recursiv = $(this).attr('data-recursiv');
                var filetype = $(this).attr('data-filetype');
                
                $.ajax({
                    url: frontendajax.ajaxurl,
                    data: {
                        'action'    :'rrze_remote_table_ajax_request',
                        'p'         : page,
                        'count'     : pagecount,
                        'index'     : index,
                        'recursiv'  : recursiv,
                        'filetype'  : filetype,
                        'chunk'     : chunk,
                        'host'      : host,
                        'arr'       : arr
                    },
                    success:function(data) {
                        $( "#result" ).html(data);
                    },  
                    error: function(errorThrown){
                        window.alert(errorThrown);
                    }
                }); 
            });

        });
        </script>
        <?php } 
 

    
    
    public function rrze_remote_table_ajax_request() {
        
        if ( isset($_REQUEST) ) {
            
            $number_of_chunks = $_REQUEST['chunk'];

            $data = array_chunk($_REQUEST['arr'], $number_of_chunks);

            if (null !== $_REQUEST['p']) {
                if ($_REQUEST['p'] > $_REQUEST['count']) {
                    die('<span style="color:#FF0000">Error: Page Does Not Exist</span>');
                }
                $i = $_REQUEST['p'] - 1;
            }
            else {
                $i = 0;
            }
            
            $table = '<table>';

            $id = uniqid();

            foreach ($data[$i] as $key => $value) {

                $table .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $_REQUEST['host'] . '/' . $_REQUEST['index'] . (($_REQUEST['recursiv'] == 1) ? '' : '/') . $value . '">' . basename($value) . '</a></td></tr>';

            }

            $table .= '</table>';
            echo $table;
        }
       
       die();
    }
}