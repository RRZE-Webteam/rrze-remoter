<?php
echo '<h1>ImageTable</h1>';

$table = '<table>';

$id = uniqid();

$count = 0;

foreach ($this->remote_data as $key => $value) {
    
    if($count % 3 == 0) $table .= '<tr>';

    $table .= '<td width="33%"><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host'] . $value['image'] . '"><img src="http://'. $url['host'] . $value['image'] . '" style="width:100%;height:auto" alt=""/></a></td>';
    
    $count++;
    
    if($count % 3 == 0) $table .= '</tr>';
    
   
}

$table .= '</table>';

echo $table;