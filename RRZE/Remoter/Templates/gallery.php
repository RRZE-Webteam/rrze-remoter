<?php
namespace RRZE\Remoter\Templates;

use RRZE\Remoter\Helper;

defined('ABSPATH') || exit;

$stylesheet = get_stylesheet();
$themes = array('FAU-Einrichtungen', 'FAU-Natfak', 'FAU-Philfak', 'FAU-RWFak', 'FAU-Techfak', 'FAU-Medfak');
if (in_array($stylesheet, $themes)) {
    global $usejslibs;
    $usejslibs['flexslider'] = true;
}

$id = Helper::createHash(10);

$output = '<div id="slider-' . $id . '" class="image-gallery-slider">';
$output  .= '<ul class="slides">';

foreach ($data as $key => $value) {

    $path = $apiurl . $value['dir'] . $value['name'] . '';
    $imginfo = getimagesize($path, $info);
    $iptcdata = iptcparse($info["APP13"]);

    $output .= '<li><img src="' . $apiurl . $value['dir'] . $value['name'] . '"/>';
    $output .= '<div class="gallery-image-caption">';
    $output .= '<span class="linkorigin">';
    $output .= '(<a href="' . $apiurl . $value['dir'] . $value['name'] . '" title="' . $iptcdata["2#120"][0] . '" class="lightbox" rel="lightbox-' . $id . '">' . __('Enlarge', 'rrze-remoter') . '</a>)';
    if ($gallerytitle && $gallerydescription) {
        $output .= '<div>' . $iptcdata["2#105"][0] . '<br>' . $iptcdata["2#120"][0] . '</div></span>';
    } elseif ($gallerytitle && !$gallerdescription) {
        $output .= '<div>' . $iptcdata["2#105"][0] . '</div></span>';
    } elseif (!$gallerytitle && $gallerydescription) {
        $output .= '<div>' . '<br>' . $iptcdata["2#120"][0] . '</div></span>';
    } elseif (!$gallerytitle && !$gallerdescription) {
        $output .= '<div></div>';
    }
    $output .= '</li>';
}

$output .= '</ul></div>' . PHP_EOL;

$output .= '<div id="carousel-' . $id . '" class="image-gallery-carousel">';
$output .= '<ul class="slides">' . PHP_EOL;

foreach ($data as $key => $value) {
    $output .= '<li><img src="' . $apiurl . $value['dir'] . $value['name'] . '" width="120" height="80" alt=""/></li>' . PHP_EOL;
}

$output .= '</ul></div>' . PHP_EOL;
echo $output;
?>
<script>
    jQuery(document).ready(function ($) {
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