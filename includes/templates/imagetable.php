<?php

$id = uniqid();

$count = 0;

$output = '<div class="image-gallery-grid clearfix">';
$output .= '<ul class="grid">';


foreach ($this->remote_data as $key => $value) {
    $output .= '<li style="height: 120px;"><a href="https://'.  $domain . $value['dir'] . $value['name'] . '" class="lightbox" rel="lightbox-' . $id .'" ><img src="https://'. $domain . $value['dir'] . $value['name'] . '" width="120" height="47" alt=""></a></li>';
}


$output .= '</ul></div>';

echo $output;