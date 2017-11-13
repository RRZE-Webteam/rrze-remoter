<?php $meta = $data; ?>
<?php $meta_store = array(); ?>
<?php for($i = 0; $i < sizeof($meta); $i++) { ?> 

    <?php if(!empty($meta[$i]['meta'])) { ?>

        <?php $transient = get_transient('rrze-remoter-transient-table'); 

            if(empty($transient)) {
                $j = 1;
            } else {
                $j = $transient;
                $j++;
            }

        ?>
        
        <div class="accordion" id="accordion-1">
        <div class="accordion-group">
        <div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-<?php echo $j ?>" href="#collapse_<?php echo $j ?>"><?php echo (!empty($meta[$i]['meta']['directory']['titel']) ? $meta[$i]['meta']['directory']['titel'] : '');  ?></a></div>
        <div id="collapse_<?php echo $j ?>" class="accordion-body" style="display: none;">
        <div class="accordion-inner clearfix">
        
        <table>
            
            <tr><td colspan="2"><strong>Beschreibung: </strong><?php echo (!empty($meta[$i]['meta']['directory']['titel']) ? $meta[$i]['meta']['directory']['beschreibung'] : '');  ?></td></tr>

            <?php foreach($meta[$i]['meta']['directory']['file-aliases'][0] as $key => $value) { ?>

                <?php $meta_store[] = array(
                    'key'   => $value,
                    'value' => $key
                )
                ?>
                <tr><td><strong>Dateiname:</strong> <?php echo $key ?></td><td><strong> Anzeigename:</strong> <?php echo $value ?></td></tr>

            <?php } ?>
                    
        </table>
            
        </div>
        </div>
        </div>
        </div>  

        <?php set_transient( 'rrze-remoter-transient-table', $j, DAY_IN_SECONDS ); ?>
    
    <?php } ?>

<?php } ?>

<?php

date_default_timezone_set('Europe/Berlin');

$url = parse_url(get_post_meta($post->ID, 'url', true)); 

$number_of_chunks = (int)$this->remote_server_shortcode['itemsperpage'];

$dataFirstPage = $this->remote_data;

foreach($dataFirstPage as $key => $value) {
    if($value['name'] === '.meta.txt') { 
        unset($dataFirstPage[$key]);
        $dataChunk = array_values($dataFirstPage);
    }
}

$data = array_chunk($dataChunk, $number_of_chunks);

$this->res = $this->remote_data; 

$this->meta = $meta_store;

$pagecount = count($data);

function getHeaderDataPagination($columns) {
    $columns = explode(",", $columns);
    return $columns;
}

function createTablePagination($columns, $data, $link, $url, $itemsperpage, $meta_store) {
    
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
                        //$t .= '<td>' . formatSize($data[$i][$j]['size']) . '</td>';
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
                        //$t .= '<td>' . getFolder($data[$i][$j]['dir']) . '</td>';
                        break;
                    case 'name':
                        if ($link) {
                            $key = array_search(basename($data[$i][$j]['path']), array_column($meta_store, 'value'));
                            
                            if($key == 0 || $key > 0) {
                              $t .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $url['host'] . $data[$i][$j]['image'] . '">' . $meta_store[$key]['key'] . '</a></td>';
                            } else {
                              $t .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $url['host'] . $data[$i][$j]['image'] . '">' . basename($data[$i][$j]['path']) . '</a></td>';; 
                            }
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
$table_var['filetype']  = $shortcodeValues['filetype'];
$table_var['recursive'] = $shortcodeValues['recursive'];
$table_var['fileindex'] = $shortcodeValues['fileIndex'];
$table_var['chunks']    = $number_of_chunks;
$table_var['pagecount'] = $pagecount;
$table_var['columns']   = $shortcodeValues['showColumns'];
$table_var['link']      = $shortcodeValues['link'];
$table_var['url']       = $url['host'];

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
        $html .= 'data-link= "' . $table_var['link'] . '"'; 
        $html .= 'class="page-'. $i.'">';
        $html .= '<span class="'. ($i==1 ? 'number active' : 'number') .'">'.$i.'</span>';
        $html .= '</a>';
    }

    $html .= '</span></nav>';
    echo $html;
    
    
}

$headerColumns = getHeaderDataPagination($table_var['columns']);
$header = createTablePagination($headerColumns, $data, $table_var['link'], $url, $number_of_chunks, $meta_store);
createNavigation($table_var);


/*function formatSize($bytes) {

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
}*/
/*
function getFolder($directory) {

    $titel = explode("/", $directory);
    $folder = $titel[count($titel)-1];

    return $folder;

}*/