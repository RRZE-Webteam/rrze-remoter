<?php

$list = '<ul>';

$id = uniqid();

foreach ($this->remote_data as $key => $value) {

    $list .= '<li>' . getIcon($value['name']) . ' <a href="http://'. $domain . $value['dir'] . $value['name'] . '">' . replaceCharacter(changeUmlauts($value['name'])) . '</a> (' . getSize($value['size']) . ')</li>';

}

$list .= '</ul>';

echo $list;

function getIcon($filename) {
    //$extension = $filename;
    $extension = substr( strrchr($filename, '.'), 1);
    if($extension == 'pdf') { 
        return '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
    } elseif ($extension == 'pptx' || $extension =='ppt') { 
        return '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>';
    } elseif ($extension == 'docx' || $extension =='doc' ) { 
        return '<i class="fa fa-file-word-o" aria-hidden="true"></i>';
    } elseif ($extension == 'xlsx' || $extension =='xls') { 
        return '<i class="fa fa-file-excel-o" aria-hidden="true"></i>';
    } elseif ($extension == 'mpg' || $extension =='mpeg'|| $extension =='mp4' || $extension =='m4v') { 
        return '<i class="fa fa-file-movie-o" aria-hidden="true"></i>';
    } else { 
        return '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
    }
}

function getSize($size) {
    return RRZE\Remoter\Class_Help_Methods::formatSize($size);
}

function replaceCharacter($name) {
    $newName = str_replace('_',' ', $name);
    return $newName;
}

function changeUmlauts($filename) {
    
    $str = str_replace(
        array('ae','oe','ue','Ae','Oe','Ue','Ã', 'ss'), 
        array( 'ä','ö','ü','Ä','Ö','Ü','Ä', 'ß'),
        $filename
    );  

    return $str;
}