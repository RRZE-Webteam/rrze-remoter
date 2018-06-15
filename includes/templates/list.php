<?php

$list = '<ul>';

$id = uniqid();

$sortOrderby = ($orderby === 'size' ? 'size' : 'name');
$sortOrder = ($order === 'asc' ? SORT_ASC : SORT_DESC);
array_multisort(array_column($data, $sortOrderby), $sortOrder , $data);

foreach ($data as $key => $value) {
    
    if($value['extension'] == 'pdf') { 
        $icon ='<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
    } elseif ($value['extension'] == 'pptx' || $value['extension'] =='ppt') { 
        $icon ='<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>';
    } elseif ($value['extension'] == 'docx' || $value['extension'] =='doc' ) { 
        $icon ='<i class="fa fa-file-word-o" aria-hidden="true"></i>';
    } elseif ($value['extension'] == 'xlsx' || $value['extension'] =='xls') { 
        $icon ='<i class="fa fa-file-excel-o" aria-hidden="true"></i>';
    } elseif ($value['extension'] == 'mpg' || $value['extension'] =='mpeg'|| $value['extension'] =='mp4' || $value['extension'] =='m4v') { 
        $icon ='<i class="fa fa-file-movie-o" aria-hidden="true"></i>';
    } else { 
        $icon ='<i class="fa fa-file-image-o" aria-hidden="true"></i>';
    }
    
    $list.= '<li>' . $icon . ' <a href="https://'. $domain . $value['dir'] . $value['name'] . '">' . RRZE\Remoter\Class_Help_Methods::replaceCharacterList(RRZE\Remoter\Class_Help_Methods::changeUmlautsList($value['name'])) . '</a> (' . RRZE\Remoter\Class_Help_Methods::formatSize($value['size']) . ')</li>';

}

$list .= '</ul>';

echo $list;