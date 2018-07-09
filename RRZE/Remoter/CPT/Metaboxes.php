<?php

namespace RRZE\Remoter\CPT;

defined('ABSPATH') || exit;

class Metaboxes
{
    public $data;
    
    protected $apiurl;

    public function __construct()
    {
        $this->data = $this->metabox_data();
        
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
    }

    protected function metabox_data()
    {
        $data = [];
        
        $data[] = [
            'id' => '_rrze_remoter_apiurl',
            'title' => __('API URL', 'rrze-remoter'),
            'post_type' => 'remoter',
            'context' => 'normal',
            'priority' => 'high',
            'args' => [
                'id' => 'rrze-remoter-apiurl',
                'type' => 'text'
            ]
        ];

        return $data;
    }

    public function add_meta_boxes()
    {
        global $post;

        if ($post->post_type != 'remoter') {
            return;
        }
        
        foreach ($this->data as $box) {
            add_meta_box(
                $box['id'],
                $box['title'],
                [$this, 'metabox_callback'],
                $box['post_type'],
                $box['context'],
                $box['priority'],
                $box['args']
            );
        }
    }

    public function metabox_callback($post, $box)
    {
        switch ($box['args']['type']) {
            case 'text':
                $this->render_text($box, $post->ID);
                break;        
        }
    }

    protected function render_text($box, $post_id)
    {
        wp_nonce_field(plugin_basename(__FILE__), 'meta_box_inhalt_nonce');
        $value = get_post_meta($post_id, $box['id'], true);
        ?>
        <label for="<?php echo $box['args']['id']; ?>"></label>
        <input type="<?php echo $box['args']['type']; ?>" id="<?php echo $box['args']['id']; ?>" name="<?php echo $box['id']; ?>" class="regular-text" value="<?php echo esc_attr($value); ?>">
        <?php
    }

    public function save_meta_boxes($post_id)
    {
        $post = get_post($post_id);

        if ($post->post_type != 'remoter') {
            return;
        }
        
        $update = false;
        foreach ($this->data as $box) {
            $meta_key = $box['id'];
            $meta_value = isset($_POST[$meta_key]) ? $_POST[$meta_key] : null;
            if (!is_null($meta_value)) {
                if ($meta_key == '_rrze_remoter_apiurl') {
                    $meta_value = $this->validate_apiurl($meta_value);
                    $this->apiurl = $meta_value;
                }
                
                if (!is_null($meta_value)) {
                    $update = true;
                    update_post_meta($post_id, $meta_key, $meta_value);
                }
            }
        }
        
        if (!$update) {
            return;
        }
        
        $response = json_decode($this->request_apikey());
        $error = isset($response->error) ? absint($response->error) : 0;
        $value = isset($response->value) ? $response->value : 0;
        
        if (in_array($error, [60, 65]) && $value) {
            update_post_meta($post_id, '_rrze_remoter_apikey', $value);
        }

    }
    
    protected function validate_apiurl($apiurl = null)
    {
        if ($apiurl && filter_var($apiurl, FILTER_VALIDATE_URL) !== false) {
            $url_scheme = is_ssl() ? 'https://' : 'http://';
            $url_host = parse_url($apiurl, PHP_URL_HOST);
            $url = trailingslashit($url_scheme . $url_host);
        } else {
            add_action('admin_notices', [$this, 'notice_not_valid_apiurl']);
            $url = null;
        }
        return $url;
    }
    
    public function notice_not_valid_apiurl() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('Not a valid API URL', 'rrze-remoter'); ?></p>
        </div>
        <?php
    }
        
    public function request_apikey()
    {
        if (!$this->apiurl) {
            return null;
        }

        $domain = parse_url(site_url(), PHP_URL_HOST);
        
        $url = sprintf('%1$srequest.php?domain=%2$s&request=key', $this->apiurl, $domain);
        do_action('rrze.log.debug', ['plugin' => 'rrze-remoter', 'remote url request=key' => $url]);
        
        $sslverify = defined('WP_DEBUG') && WP_DEBUG ? false : true;
        
        $response = wp_remote_get($url, ['httpversion' => '1.1', 'sslverify' => $sslverify]);
        $status_code = wp_remote_retrieve_response_code($response);

        do_action('rrze.log.debug', ['plugin' => 'rrze-remoter', 'remote url request=key status code' => $status_code]);
        
        if ($status_code == 200) {
            return $response['body'];
        }
        
        return null;
    }
    
}
