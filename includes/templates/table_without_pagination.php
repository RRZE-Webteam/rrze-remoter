<?php
echo '<h3>Normale Tabellenansicht</h3>';

$data = $this->remote_data;

if (!function_exists('getHeaderData')) {
    function getHeaderData($columns) {
        $columns = explode(",", $columns);
        return $columns;
    }
}

$headerColumns = getHeaderData($show_columns);

if (!function_exists('createHeader')) {
    function createHeader($columns, $data) {
        
        $t  = '<div>';
        $t .= '<table>';
        $t .= '<tr>';
        
        foreach($columns as $key => $column) {
        
            switch($column) {
                case 'size':
                    $t .= '<th>'. ucfirst($column) .'</th>';
                    break;
                case 'type':
                    $t .= '<th>'. ucfirst($column) .'</th>';
                    break;
                case 'download':
                    $t .= '<th>'. ucfirst($column) .'</th>';
                    break;
                case 'folder':
                    $t .= '<th>'. ucfirst($column) .'</th>';
                    break;
                case 'name':
                    $t .= '<th>'. ucfirst($column) .'</th>';
                    echo '';
                case 'date':
                    $t .= '<th>'. ucfirst($column) .'</th>';
                    echo '';    
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
                        $t .= '<td>' . $data[$i]['extension'] .'</td>';
                        break;
                    case 'download':
                        $t .= '<td><a href=""  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
                        break;
                    case 'folder':
                        $t .= '<td>' . getFolder($data[$i]['dir']) . '</td>';
                        break;
                    case 'name':
                        $t .= '<td>' . basename($data[$i]['path']) .'</td>';
                        echo ''; 
                    case 'date':
                        $t .= '<td>' . $data[$i]['access_time'] .'</td>';
                        echo ''; 
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
      
$headerColumns = getHeaderData($show_columns);
$header = createHeader($headerColumns, $data);