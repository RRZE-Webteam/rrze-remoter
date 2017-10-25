<?php
echo '<h3>Normale Tabellenansicht</h3>';

$list = '<ul>';

$id = uniqid();

$output = '<div><table><tr>';
$output .= $folder_column == 1 ? '<th>Verzeichnis</th>' : '';
$output .= '<th>Name</th>';
$output .= $date_column == 1 ? '<th>Änderungsdatum</th>' : '';
$output .= $type_column == 1 ? '<th>Dateityp</th>' : '';
$output .= $size_column == 1 ? '<th>Dateigröße</th>' : '';
$output .= $download == 1 ? '<th>Download</th>' : '';
$output .= '</tr>';

/*$i = 0;




for($j = 0; $j < sizeof($this->remote_data); $j++) {
    $from_a = $this->remote_data[$j]['dir'];
    $titel_a = explode("/",$from_a);
    $folder_a[] = $titel_a[count($titel_a)-1];
}

echo '<pre>';
//print_r($folder_a);
echo '</pre>';

$unique = array_unique($folder_a);

//sort($unique);

echo '<pre>';
//print_r($unique);
echo '</pre>';

$out = array();
foreach ($this->remote_data as $key => $value){
    $out[] = array_merge((array)$folder_a[$key], $value);
}

for($z = 0; $z < sizeof($out); $z++) {
    $out[$z]['folder'] = $out[$z][0];
    unset($out[$z][0]);
}


echo '<pre>';
//print_r($out);
echo '</pre>';*/

//rsort($this->remote_data);

//natcasesort($this->remote_data);

/*usort($this->remote_data, function($x, $y) {
     return strcasecmp($x['name'] , $y['name']);
});*/

sort($this->remote_data);

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
    
    $file = 'http://'. $url['host'] . $value['image'] . '';
    $test = getimagesize('http://remoter.dev/images/butterfly_abstract_colorful_patterns_97225_2560x1440.jpg', $info);
    $iptc = iptcparse($info["APP13"]);
    if (is_array($iptc)) {
        $data['headline']             = $iptc["2#105"][0];
        $data['documentTitle']        = $iptc["2#120"][0];
        $data['graphic_name']         = $iptc["2#005"][0];
        $data['urgency']              = $iptc["2#010"][0];
        $data['category']             = $iptc["2#015"][0];
        $data['supp_categories']      = $iptc["2#020"][0];
        $data['spec_instr']           = $iptc["2#040"][0];
        $data['creation_date']        = $iptc["2#055"][0];
        $data['authorByline']         = $iptc["2#080"][0];
        $data['authorTitle']          = $iptc["2#085"][0];
        $data['city']                 = $iptc["2#090"][0];
        $data['state']                = $iptc["2#095"][0];
        $data['country']              = $iptc["2#101"][0];
        $data['otr']                  = $iptc["2#103"][0];
        $data['source']               = $iptc["2#110"][0];
        $data['photo_source']         = $iptc["2#115"][0];
    }
    
    /*echo '<pre>';
    //print_r($data);
    echo '</pre>';*/
    
   /* $from = $this->remote_data[$i]['dir'];
    $titel = explode("/",$from);
    $folder = $titel[count($titel)-1];
    $i++;
    */
    
   
    
    $imageicon = $value['extension'] == 'pdf' ? '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>' : '<i class="fa fa-file-image-o" aria-hidden="true"></i>' ;
    
    $filename = $value['basename'];
    
    $output .= '<tr>';
    
    if ($folder_column)  $output .= '<td>' . $folder . '</td>';
    
    if ($link) {
        $output .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host'] . $value['image'] . '">' . substr($value['basename'], 0, strrpos($value['basename'], '.')) . '</a></td>';
    } else {
        $output .= '<td>' . ((substr($value['basename'],0,1) === 'G') ? substr($value['basename'],13) : $value['basename'] ). '</td>';
    }
    
    if ($date_column) { 
        $date = new DateTime();
        $date->setTimestamp($value['change_time']);
        $output .= '<td>' . $date->format('j. F Y') . '</td>';
    }
    
    if ($type_column) $output .= '<td>'. $imageicon . '</td>';
    if ($size_column) $output .= '<td>' . $size .  '</td>';
    if ($download) $output  .= '<td><a href="http://'. $url['host'] . $value['image'] . '" title="Rechtsklick für Download" download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
 
}

$output .= '</table></div>';

echo $output;
