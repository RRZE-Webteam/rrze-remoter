<?php

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
