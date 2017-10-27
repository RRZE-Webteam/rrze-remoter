<?php

date_default_timezone_set('Europe/Berlin');

$url = parse_url(get_post_meta($post->ID, 'url', true)); 

$number_of_chunks = (int)$this->remote_server_shortcode['itemsperpage'];

$data = array_chunk($this->remote_data, $number_of_chunks);

$this->res = $this->remote_data; 

$pagecount = count($data);

function getHeaderDataPagination($columns) {
    $columns = explode(",", $columns);
    return $columns;
}

function createTablePagination($columns, $data, $link, $url, $itemsperpage) {

    $id = uniqid();
    $itemscount = count($data[0]);

    $t  = '<div id="result">';
    $t .= '<table>';
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
    for($i = 0;  $i < 1; $i++) {

        for($j = 0; $j < $itemscount; $j++) {
            
            $t .= '</tr>';
          
            foreach($columns as $key => $column) {

              switch($column) {
                  case 'size':
                      $t .= '<td>' . formatSize($data[$i][$j]['size']) . '</td>';
                      break;
                  case 'type':
                      $extension = $data[$i][$j]['extension'];
                      if($extension == 'pdf') {
                          $t .= '<td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>';
                      }elseif($extension == 'pptx') {
                          $t .= '<td align="center"><i class=" file-powerpoint-o" aria-hidden="true"></i></td>'; 
                      }else{
                          $t .= '<td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>'; 
                      }
                      break;
                  case 'download':
                      $t .= '<td><a href="http://' . $url['host'] . $data[$i][$j]['image'] . '"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
                      break;
                  case 'folder':
                      $t .= '<td>' . getFolder($data[$i][$j]['dir']) . '</td>';
                      break;
                  case 'name':
                      if ($link) {
                        $t .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $url['host'] . $data[$i][$j]['image'] . '">' .  basename($data[$i][$j]['path']) . '</a></td>';    
                      } else {
                        $t .= '<td>' . basename($data[$i][$j]['path']) .'</td>';  
                      }
                      break;
                  case 'date':
                      $t .= '<td>' . date('j. F Y', $data[$i][$j]['change_time']) .'</td>';
                      break; 
              }

          }

        $t .= '</tr>';
        }
    }

    $t .= '</table></div>';
    echo $t;
}

$table_var = array();
$table_var['filetype']  = $filetype;
$table_var['recursive'] = $recursiv;
$table_var['fileindex'] = $file_index;
$table_var['url']       = $url['host'];
$table_var['chunks']    = $number_of_chunks;
$table_var['pagecount'] = $pagecount;
$table_var['columns']   = $show_columns;



function createNavigation(array $table_var) {
    
    $html = '<nav class="pagination pagebreaks" role="navigation"><h3>Seite:</h3><span class="subpages">';

    for ($i = 1; $i <= $table_var['pagecount']; $i++) {

        $html .= '<a data-filetype="' . $table_var['filetype'] . '" href="#get_list"';
        $html .= 'data-recursiv="' . $table_var['recursive'] . '"';
        $html .= 'data-index="' . $table_var['fileindex'] . '"';
        $html .= 'data-host="' . $table_var['url'] . '"';
        $html .= 'data-chunk="' . $table_var['chunks'] . '"';
        $html .= 'data-pagecount-value= "' . $table_var['pagecount'] . '"'; 
        $html .= 'data-columns= "' . $table_var['columns'] . '"'; 
        $html .= 'class="page-'. $i.'">';
        $html .= '<span class="'. ($i==1 ? 'number active' : 'number') .'">'.$i.'</span>';
        $html .= '</a>';
    }

    $html .= '</span></nav>';
    echo $html;
    
    
}

$headerColumns = getHeaderDataPagination($show_columns);
$header = createTablePagination($headerColumns, $data, $link, $url, $number_of_chunks);
createNavigation($table_var);


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

function getFolder($directory) {

    $titel = explode("/", $directory);
    $folder = $titel[count($titel)-1];

    return $folder;

}