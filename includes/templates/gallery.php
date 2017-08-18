<?php

echo '<pre>';
print_r($this->remote_data);
echo '</pre>';
/*
$id = uniqid();

echo '<pre><h3>Galerie</h3>';
echo '<div id="slider-'. $id . '" class="image-gallery-slider">';
echo '<ul>';

foreach ($this->remote_data as $key => $value) {
    
    echo '<li><img src="http://'. $url['host'] . '/' . $file_index . '/' . $value . '">';
    echo '<div class="gallery-image-caption">Bild in Originalgröße (1153px). Ausrichtung keine.<br /><span class="linkorigin">(<a href="http://'. $url['host'] . '/' . $file_index . $value . '"  title="Bild in Originalgröße (1153px). Ausrichtung keine." class="lightbox" rel="lightbox-' . $id . '">Vergrößern</a>)</span></div>';
    echo '</li>';

}

echo '</ul></div>';

echo '<div id="carousel-'. $id . '" class="image-gallery-slider">';
echo '<ul class="slides">';

foreach ($this->remote_data as $key => $value) {
  
    echo '<li><img width="120" height="80" src="http://'. $url['host'] . '/' . $file_index . '/' . $value . '"></li>';
    echo '<script type="text/javascript">';
    echo 'jQuery(document).ready(function($) {
    $("#carousel-' . $id . '").flexslider({}); $("#slider-' . $id . '").flexslider({})';
    echo '</script>';
    
}

echo '</ul></div></pre>';

echo '<p>';
*/

/*$id = uniqid();

echo '<h3>Galerie</h3>';
echo '<div id="slider-'. $id . '" class="image-gallery-slider">';
echo '<ul>';

foreach ($this->remote_data as $key => $value) {
    
    echo '<li><img src="http://'. $url['host'] . '/' . $file_index . '/' . $value . '">';
    echo '<div class="gallery-image-caption">Bild in Originalgröße (1153px). Ausrichtung keine.<br /><span class="linkorigin">(<a href="http://'. $url['host'] . '/' . $file_index . $value . '"  title="Bild in Originalgröße (1153px). Ausrichtung keine." class="lightbox" rel="lightbox-' . $id . '">Vergrößern</a>)</span></div>';
    echo '</li>';

}

echo '</ul></div>';

echo '<div id="carousel-'. $id . '" class="image-gallery-slider">';
echo '<ul class="slides">';

foreach ($this->remote_data as $key => $value) {
  
    echo '<li><img width="120" height="80" src="http://'. $url['host'] . '/' . $file_index . '/' . $value . '"></li>';
    echo '<script type="text/javascript">';
    echo 'jQuery(document).ready(function($) {
    $("#carousel-' . $id . '").flexslider({}); $("#slider-' . $id . '").flexslider({})';
    echo '</script>';
    
}

echo '</ul></div>';

echo '<p>';

*/

echo '<h3>Galerie</h3>';
    
$id = uniqid();

echo '<div id="slider-' . $id . '" class="image-gallery-slider">
<ul class="slides">';
foreach ($this->remote_data as $key => $value) {
echo '<li><img src="http://'. $url['host'] . '/' . $file_index . (($recursiv == 1) ? '' : '/') . $value . '"/>
<div class="gallery-image-caption">Bild in Originalgröße (1153px). Ausrichtung keine.<br /><span class="linkorigin">(<a href="http://'. $url['host'] . '/' . $file_index . (($recursiv == 1) ? '' : '/') . $value . '"  title="Bild in Originalgröße (1153px). Ausrichtung keine." class="lightbox" rel="lightbox-601862376">Vergrößern</a>)</span></div>
</li>';
}

echo '
</ul>
</div>';
        
echo '<div id="carousel-' . $id . '" class="image-gallery-carousel">
<ul class="slides">';
foreach ($this->remote_data as $key => $value) {
echo '<li><img src="http://'. $url['host'] . '/' . $file_index . (($recursiv == 1) ? '' : '/') . $value . '" width="120" height="80" alt=""/></li>';
}
$html = '</ul></div>';
echo $html;

/*echo '<script type="text/javascript">';
echo 'jQuery(document).ready(function($) {
$("#carousel-' . $id . '").flexslider({}); $("#slider-' . $id . '").flexslider({})';
echo '</script>';


*/
/*echo '<h3>Galerie</h3>';
    
$id = uniqid();

echo '<div id="slider-' . $id . '" class="image-gallery-slider">
<ul class="slides">
<li><img src="http://remoter.dev/images/abstraction_geometry_shapes_colors_93400_2560x1440.jpg" width="723" height="470" alt=""/>
<div class="gallery-image-caption">Bild in Originalgröße (1153px). Ausrichtung keine.<br /><span class="linkorigin">(<a href="http://remoter.dev/images/abstraction_geometry_shapes_colors_93400_2560x1440.jpg"  title="Bild in Originalgröße (1153px). Ausrichtung keine." class="lightbox" rel="lightbox-601862376">Vergrößern</a>)</span></div>
</li>
<li><img src="http://remoter.dev/images/abstraction_geometry_shapes_colors_93400_2560x1440.jpg" width="705" height="470" alt=""/>
<div class="gallery-image-caption"><span class="linkorigin">(<a href="http://remoter.dev/images/abstraction_geometry_shapes_colors_93400_2560x1440.jpg"  class="lightbox" rel="lightbox-601862376">Vergrößern</a>)</span></div>
</li>
<li><img src="http://remoter.dev/images/abstraction_geometry_shapes_colors_93400_2560x1440.jpg" width="705" height="470" alt=""/>
<div class="gallery-image-caption">Conforama-Patinadora, CC-BY 3.0<br /><span class="linkorigin">(<a href="http://remoter.dev/images/abstraction_geometry_shapes_colors_93400_2560x1440.jpg"  title="Conforama-Patinadora, CC-BY 3.0" class="lightbox" rel="lightbox-601862376">Vergrößern</a>)</span></div>
</li>
</ul>
</div>
        
<div id="carousel-' . $id . '" class="image-gallery-carousel">
<ul class="slides">
<li><img src="http://remoter.dev/images/abstraction_geometry_shapes_colors_93400_2560x1440.jpg" width="120" height="80" alt=""/></li>
<li><img src="http://remoter.dev/images/abstraction_geometry_shapes_colors_93400_2560x1440.jpg" width="120" height="80" alt=""/></li>
<li><img src="http://remoter.dev/images/abstraction_geometry_shapes_colors_93400_2560x1440.jpg" width="120" height="80" alt=""/></li>
</ul>
</div>'; */?>
        

<?php /* Auskommentieren verursacht JQuery Fehler **/ ?>


<?php /*
<script type="text/javascript">
jQuery(document).ready(function($) {
  $("#carousel-<?php echo $id ?>").flexslider({
    maxItems: 7,
    selector: "ul > li",
    animation: "slide",
    keyboard: true,
    multipleKeyboard: true,
    directionNav: true,
    controlNav: true,
    pausePlay: false,
    slideshow: false,
    asNavFor: "#slider-<?php echo $id ?>",
    itemWidth: 125,
    itemMargin: 5
  });
  $("#slider-<?php echo $id ?>").flexslider({
    selector: "ul > li",
    animation: "slide",
    keyboard: true,
    multipleKeyboard: true,
    directionNav: false,
    controlNav: false,
    pausePlay: false,
    slideshow: false,
    sync: "#carousel-<?php echo $id ?>"
  });
});</script>

*/
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
 
 alert('hello');
});</script>

