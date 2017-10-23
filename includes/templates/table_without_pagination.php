<?php

//echo '<h3>Listenansicht</h3>';

echo '<pre>';
//print_r($this->remote_data);
echo '</pre>';

$list = '<ul>';

$id = uniqid();

$output = '<div><table><tr>';
$output .= '<th>Name</th>';
$output .= '<th>Änderungsdatum</th>';
$output .= '<th>Dateityp</th>';
$output .= '<th>Dateigröße</th>';
$output .= '<th>Download</th>';
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

    $path = 'http://'. $url['host'] . $value['image'];
    $output .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host'] . $value['image'] . '">' . substr($value['basename'], 0, strrpos($value['basename'], '.')) . '</a></td>';
    $output .= '<td>' . date('Y-m-d H:i:s', $value['change_time']) . '</td>';
    $output .= '<td>' . $value['extension'] . '</td><td>' . $size .  '</td>';
    //$output .= '<td align="center"><a></a><div class="download-file" href="#" data-host="' . $url['host'] . '" data-image="' . $value['image'] . '" data-name="' . $value['name'] . '"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></div></td></tr>';
    //$output .= '<td><a href="' . plugins_url( 'includes/templates/gallery.php?send=15', dirname(__FILE__) ) . '" download>Download</a></td>';
    $output .= '<td><a href="javascript:void(0)" onclick="loadProducts(\'' . $path . '\');"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
}

$output .= '</table></div>';

echo $output;

?>

 <script type= "text/javascript">
    function loadProducts(path){
        alert(path);
        var iframe = document.createElement('iframe');
        iframe.id = "IFRAMEID";
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
        iframe.src = path+'?' + $.param($scope.filtro);
        iframe.addEventListener("load", function () {
             console.log("FILE LOAD DONE.. Download should start now");
        });
    }
</script>