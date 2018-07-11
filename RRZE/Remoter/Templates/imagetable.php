<?php
namespace RRZE\Remoter\Templates;

use RRZE\Remoter\Helper;

defined('ABSPATH') || exit;

$id = Helper::createHash(10);

$count = 0;

$output = '<div class="image-gallery-grid clearfix">';
$output .= '<ul class="grid">';


foreach ($data as $key => $value) {
    $output .= '<li style="height: 120px;"><a href="'.  $apiurl . $value['dir'] . $value['name'] . '" class="lightbox" rel="lightbox-' . $id .'" ><img src="'. $apiurl . $value['dir'] . $value['name'] . '" width="120" height="47" alt=""></a></li>';
}


$output .= '</ul></div>';

echo $output;