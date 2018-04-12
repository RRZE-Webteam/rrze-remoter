<?php

$current_theme = wp_get_theme();
$themes = array('FAU-Einrichtungen', 'FAU-Natfak', 'FAU-Philfak', 'FAU-RWFak', 'FAU-Techfak', 'FAU-Medfak');
if(!in_array($current_theme, $themes)) {
echo '';
} else {
global $usejslibs;
$usejslibs['flexslider'] = true;
}


$id = uniqid();
$data = $this->remote_data;

function createSlider($data, $domain, $id, $gallerytitle, $gallerydescription) {

    $g  = '<div id="slider-' . $id . '" class="image-gallery-slider">';
    $g .= '<ul class="slides">';
    
    foreach ($data as $key => $value) {
        
        $path       = 'http://'. $domain . $value['dir'] . $value['name'] . '';
        $imginfo    = getimagesize($path, $info);
        $iptcdata    = iptcparse($info["APP13"]);
        
        $g .= '<li><img src="http://'. $domain . $value['dir'] . $value['name'] . '"/>';
        $g .= '<div class="gallery-image-caption">';
        $g .= '<span class="linkorigin">';
        $g .= '(<a href="http://'. $domain . $value['dir'] . $value['name'] . '" class="lightbox" rel="lightbox-' . $id . '">Vergrößern</a>)';
        if($gallerytitle && $gallerydescription) {
            $g .= '<div>' . $iptcdata["2#105"][0]  . '<br>' . $iptcdata["2#120"][0] . '</div></span>';
        } elseif($gallerytitle && !$gallerdescription) {
            $g .= '<div>' . $iptcdata["2#105"][0] .'</div></span>';
        } elseif(!$gallerytitle && $gallerydescription) {
            $g .= '<div>' . '<br>' . $iptcdata["2#120"][0] . '</div></span>';
        } elseif(!$gallerytitle && !$gallerdescription) {
            $g .= '<div></div>';
        }    
        $g .= '</li>';
    
    }

    $g .= '</ul></div>';
    echo $g;
    
}



function createCarousel($data, $domain, $id) {
        
    $c = '<div id="carousel-' . $id . '" class="image-gallery-carousel">';
    $c .= '<ul class="slides">';
    
    foreach ($data as $key => $value) {
        $c .= '<li><img src="http://'. $domain . $value['dir'] . $value['name'] . '" width="120" height="80" alt=""/></li>';
    }
        
    $c .='</ul></div>';
    echo $c;
}

/*function getIptcData($data, $domain) {
    
    $path       = 'http://'. $domain . $value['image'] . '';
    $imginfo    = getimagesize($path, $info);
    $iptcdata    = iptcparse($info["APP13"]);
    
    if (is_array($iptcdata)) {
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
    
    return $data;

}*/

createSlider($data, $domain, $id, $gallery_title, $gallery_description);
createCarousel($data, $domain, $id);

?>
<?php function setFlexslider($id) { ?>
<script>
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
});
</script>
<?php } setFlexslider($id); ?>