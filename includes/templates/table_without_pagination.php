<?php

if (!function_exists('getHeaderData')) {
    function getHeaderData($columns) {
        $shortcodeColumns = explode(",", $columns);
        return $shortcodeColumns;
    }
}

if (!function_exists('createTable')) {
    function createTable($columns, $data, $link, $url, $showheader) {
        
        $id = uniqid();
        
        if($showheader) {
            
            $t = '<table>';
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
        }
        
        if(!$showheader) {
            $t = '';
        }
        
        for($i = 0; $i < sizeof($data); $i++) {
            
            $t .= '<tr>';
        
            foreach($columns as $key => $column) {

               switch($column) {
                    case 'size':
                        $t .= '<td>' . formatSize($data[$i]['size']) . '</td>';
                        break;
                    case 'type':
                        $extension = $data[$i]['extension'];
                        if($extension == 'pdf') {
                            $t .= '<td><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>';
                        }elseif($extension == 'pptx') {
                            $t .= '<td><i class=" file-powerpoint-o" aria-hidden="true"></i></td>'; 
                        }else{
                            $t .= '<td><i class="fa fa-file-image-o" aria-hidden="true"></i></td>'; 
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
                          $t .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $url['host'] . $data[$i]['image'] . '">' .  basename($data[$i]['path']) . '</a></td>';    
                        } else {
                          $t .= '<td>' . basename($data[$i]['path']) .'</td>';  
                        }
                        break;
                    case 'date':
                        $t .= '<td>' . date('j F Y', $data[$i]['change_time']) .'</td>';
                        break; 
                }

            }
            
            $t .= '</tr>';
            
        }
        
        if($showheader) {
            $t .= '</table>';
        }
        
        return $t;
        
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
      
$headerColumns = getHeaderData($show_columns);
echo createTable($headerColumns, $data, $link, $url, $header);