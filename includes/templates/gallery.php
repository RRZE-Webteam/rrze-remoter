<?php

echo '<pre>';
print_r($this->remote_data);
echo '</pre>';


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
echo '</ul></div>';
?>
        

<?php ?>
<script type="text/javascript">
jQuery(document).ready(function($) {
  $("#carousel-<?php echo $id ?>").flexslider({
    maxItems: 5,
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