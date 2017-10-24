<?php
echo '<h3>Normale Tabellenansicht</h3>';

$list = '<ul>';

$id = uniqid();

$output = '<div><table><tr>';
$output .= '<th>Name</th>';
$output .= '<th>Änderungsdatum</th>';
$output .= '<th>Dateityp</th>';
$output .= '<th>Dateigröße</th>';
$output .= $download == 1 ? '<th>Download</th>' : '';
$output .= '</tr>';

foreach ($this->remote_data as $key => $value) {
    
    $bytes = $value['size'];
            
    if ($bytes>= 1073741824) {
        $size = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
       $size = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $size = number_format($bytes / 1024, 0) . ' KB';
    } elseif ($bytes > 1) {
        $size = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $size = '1 byte';
    } else {
        $size = '0 bytes';
    }
    
    $imageicon = $value['extension'] == 'pdf' ? '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>' : '<i class="fa fa-file-image-o" aria-hidden="true"></i>' ;
    $output .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host'] . $value['image'] . '">' . substr($value['basename'], 0, strrpos($value['basename'], '.')) . '</a></td>';
    $output .= '<td>' . date('Y-m-d H:i:s', $value['change_time']) . '</td>';
    $output .= '<td>'. $imageicon .' '. $value['extension'] . '</td><td>' . $size .  '</td>';
    if ($download) $output  .= '<td align="center"><a href="http://'. $url['host'] . $value['image'] . '" title="Rechtsklick für Download" download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
 
}

$output .= '</table></div>';

echo $output;
