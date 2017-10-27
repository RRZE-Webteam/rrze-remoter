<?php

$data = $this->remote_data;
$this->glossary_array = $this->remote_data;

function createLetters() {
   
    $letters = array(
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
    );
    
    return $letters;
    
}

function getUsedLetters($data) {
   
    foreach ($data as $file) {
        $files[] = substr($file['name'], 0, 1);
    }

    $unique = array_unique($files);

    sort($unique);
    
    return $unique;
    
}

function getActiveLetters($letters, $unique, $url, $columns) {
    
    $html  = '<h3>Glossar</h3>';
    $html .= '<nav class="pagination pagebreaks" role="navigation"><span class="subpages">';

    foreach ($letters as $key => $value) {
        
        if (in_array($value, $unique)) {

            $html .= '<a href="#letter-' . $value . '"data-columns="' . $columns . '"data-host="' . $url['host'] . '" data-letter="' . $value . '"><span class="'. ($value == $unique[0] ? 'number active' : 'number') .'">'.$value.'</span></a>';

        } else {

           $html .= '<span class="muted">'.$value.'</span>';

        }
    }

    $html .= '</span></nav>';
    echo $html; 
    
}

function sortArray($data, $unique) {
    
    $filenames = array(); 
    
    foreach ($data as $file) {
        $filenames[] = $file['name'];
    }

    array_multisort($filenames, SORT_ASC, $data);

    foreach ( $data as $key => $value ) {
        if ( substr($value['name'], 0, 1) != $unique[0]) {
            unset( $data[$key]);
        }
    }
    
    $array_reindexed = array_values($data);
    
    return $array_reindexed;
    
}

if (!function_exists('getHeaderDataGlossary')) {
    function getHeaderDataGlossary($columns) {
        $columns = explode(",", $columns);
        return $columns;
    }
}

if (!function_exists('createTableGlossary')) {
    function createTableGlossary($columns, $data, $url, $link) {
        $id = uniqid();
        $t  = '<div id="glossary"><table>';
        $t .= '<tr>';

        foreach($columns as $key => $column) {

            switch($column) {
                case 'size':
                    $t .= '<th>Dateigröße</th>';
                    break;
                case 'type':
                    $t .= '<th>Dateityp</th>';
                    break;
                case 'download':
                    $t .= '<th>Download</th>';
                    break;
                case 'folder':
                    $t .= '<th>Ordner</th>';
                    break;
                case 'name':
                    $t .= '<th>Name</th>';
                    break;
                case 'date':
                    $t .= '<th>Datum</th>';
                    break;   
            }
        }

        $t .= '</tr>';
        
        for($i = 0; $i < sizeof($data); $i++) {
            
            $t .= '</tr>';
        
            foreach($columns as $key => $column) {

                switch($column) {
                    case 'size':
                        $t .= '<td>' . formatSize($data[$i]['size']) . '</td>';
                        break;
                    case 'type':
                        $extension = $data[$i]['extension'];
                        if($extension == 'pdf') {
                            $t .= '<td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>';
                        }elseif($extension == 'pptx') {
                            $t .= '<td align="center"><i class=" file-powerpoint-o" aria-hidden="true"></i></td>'; 
                        }else{
                            $t .= '<td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>'; 
                        }
                        break;
                    case 'download':
                        $t .= '<td><a href="http://' . $url['host'] . $data[$i]['image'] . '"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
                        break;
                    case 'folder':
                        $t .= '<td>' . getFolder($data[$i]['dir']) . '</td>';
                        break;
                    case 'name':
                        if ($link) {
                          $t .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $url['host'] . $data[$i]['image'] . '">' .  basename($data[$i]['image']) . '</a></td>';    
                        } else {
                          $t .= '<td>' . basename($data[$i]['path']) .'</td>';  
                        }
                        break;
                    case 'date':
                        $t .= '<td>' . date('j F Y', $data[$i]['access_time']) .'</td>';
                        break; 
                }

            }
            
            $t .= '</tr>';
            
        }
       
        $t .= '</table></div>';
        echo $t;
    }
}

if (!function_exists('formatSize')) {
    function formatSize($bytes) {

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
        
        return $size;
    }
}


if (!function_exists('getFolder')) {
    function getFolder($directory) {
 
        $titel = explode("/", $directory);
        $folder = $titel[count($titel)-1];
        
        return $folder;
    
    }
}
        
$columns = getHeaderDataGlossary($show_columns);
$letters = createLetters();
$unique = getUsedLetters($data);
getActiveLetters($letters, $unique, $url, $show_columns);
$array_reindexed = sortArray($data, $unique);
createTableGlossary($columns, $array_reindexed, $url, $link);