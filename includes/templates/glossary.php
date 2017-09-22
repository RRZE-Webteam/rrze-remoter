<?php

$letters = array(
    'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
);

$output = '<div class="fau-glossar">';
$output .= '<ul class="letters" aria-hidden="true">';

foreach ($letters as $key => $value) {
    $output .= '<li class="filled"><a href="#letter-' . $value . '"data-host="' . $url['host'] . '" data-letter="' . $value . '">' . $value . '</a></li>';

}

$output .= '</ul></div>';

echo $output; 

$this->glossary_array = $this->remote_data;

$glossary_init = $this->remote_data;

$filenames = array(); 

foreach ($glossary_init as $file) {
    $filenames[] = $file['name'];
}

array_multisort($filenames, SORT_ASC, $glossary_init);

foreach ( $glossary_init as $key => $value ) {
    if ( substr($value['name'], 0, 1) != 'A') {
        unset( $glossary_init[$key]);
    }
}

$array_reindexed = array_values($glossary_init);

$id = uniqid();

$table = '<div id="glossary"><table><tr>';
$table .= '<th>Name</th>';
$table .= '<th>Änderungsdatum</th>';
$table .= '<th>Dateityp</th>';
$table .= '<th>Dateigröße</th>';
$table .= '</tr>';


 foreach ($array_reindexed as $key => $value) {
                
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

    $table .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host']  . $value['image'] . '">';
    $table .=  substr($value['basename'], 0, strrpos($value['basename'], '.')) . '</a>';
    $table .= '</td><td>' . date('Y-m-d H:i:s', $value['change_time']) . '</td>';
    $table .= '<td>' . $value['extension'] . '</td>';
    $table .= '<td>' . $size.  '</td></tr>';
}

$table .= '</table></div>';
echo $table;