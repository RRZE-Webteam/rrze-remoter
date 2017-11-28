<?php

$id = uniqid();

$count = 0;

$output = '<div class="image-gallery-grid clearfix">';
$output .= '<ul class="grid">';


foreach ($this->remote_data as $key => $value) {
    $output .= '<li style="height: 183px;"><a href="http://'. $domain . $value['image'] . '" class="lightbox" rel="lightbox-' . $id .'" ><img src="http://'. $domain . $value['image'] . '" width="120" height="47" alt=""></a></li>';
}


$output .= '</ul></div>';

echo $output;