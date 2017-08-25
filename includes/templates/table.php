<?php



$url = parse_url(get_post_meta($post->ID, 'url', true)); 

$number_of_chunks = 3;

$data = array_chunk($this->remote_data, $number_of_chunks);

$this->res = $this->remote_data; 

echo '<pre>';
print_r($this->remote_data);
echo '</pre>';

$pagecount = count($data);



echo '<h3>Tabellenansicht mit Pagination</h3>';

$i = 0;

$id = uniqid();

$output = '<div id="result"><table>';

foreach ($data[$i] as $key => $value) {

    $output .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host'] . '/' . $file_index . (($recursiv == 1) ? '' : '/') . $value . '">' . basename($value) . '</a></td></tr>';

}

$output .= '</table></div>';

echo $output;

$html = '<nav class="pagination pagebreaks" role="navigation"><h3>Seite:</h3><span class="subpages">';

for ($i = 1; $i <= $pagecount; $i++) {

    $html .='<a data-filetype="' . $filetype . '" data-recursiv="' . $recursiv . '" data-index="' . $file_index . '" data-host="' . $url['host'] . '" data-chunk="' . $number_of_chunks . '" data-pagecount-value= "' . $pagecount . '" class="page-'. $i.'" href="#sign_up"><span class="'. ($i==1 ? 'number active' : 'number') .'">'.$i.'</span></a>';

}

$html .= '</span></nav>';
echo $html;