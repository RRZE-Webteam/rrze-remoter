<?php

echo '<h3>Listenansicht</h3>';

echo '<pre>';
print_r($this->remote_data);
echo '</pre>';

$list = '<ul>';

$id = uniqid();

foreach ($this->remote_data as $key => $value) {

    $list .= '<li><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host'] . '/' . $file_index . (($recursiv == 1) ? '' : '/') . $value . '">' . basename($value) . '</a></li>';

}

$list .= '</ul>';

echo $list;

