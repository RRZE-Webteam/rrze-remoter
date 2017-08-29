<?php

/*echo '<pre>';
print_r($this->remote_data);
echo '</pre>';*/

$url = parse_url(get_post_meta($post->ID, 'url', true)); 

$number_of_chunks = (int)$this->remote_server_shortcode['chunk'];

$data = array_chunk($this->remote_data, $number_of_chunks);

$this->res = $this->remote_data; 

$pagecount = count($data);

echo '<h3>Tabellenansicht mit Pagination</h3>';

$i = 0;

$id = uniqid();

$output = '<div id="result"><table><tr>';
$output .= '<th>Name</th>';
$output .= '<th>Änderungsdatum</th>';
$output .= '<th>Dateityp</th>';
$output .= '<th>Dateigröße</th>';
$output .= '</tr>';


foreach ($data[$i] as $key => $value) {
    
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

    $output .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host'] . $value['image'] . '">' . substr($value['basename'], 0, strrpos($value['basename'], '.')) . '</a></td><td>' . date('Y-m-d H:i:s', $value['change_time']) . '</td><td>' . $value['extension'] . '</td><td>' . $size .  '</td></tr>';
}

$output .= '</table></div>';

echo $output;

$html = '<nav class="pagination pagebreaks" role="navigation"><h3>Seite:</h3><span class="subpages">';

for ($i = 1; $i <= $pagecount; $i++) {

    $html .= '<a data-filetype="' . $filetype . '" href="#get_list"';
    $html .= 'data-recursiv="' . $recursiv . '"';
    $html .= 'data-index="' . $file_index . '"';
    $html .= 'data-host="' . $url['host'] . '"';
    $html .= 'data-chunk="' . $number_of_chunks . '"';
    $html .= 'data-pagecount-value= "' . $pagecount . '"'; 
    $html .= 'class="page-'. $i.'">';
    $html .= '<span class="'. ($i==1 ? 'number active' : 'number') .'">'.$i.'</span>';
    $html .= '</a>';
}

$html .= '</span></nav>';
echo $html;