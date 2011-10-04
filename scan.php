<?php

function werks($base,$ext, &$count=0, &$data=array()) { 
  $array = array_diff(scandir($base), array('.', '..')); 
  foreach($array as $value) : 
    if (is_dir($base.$value)) :
      $data = werks($base.$value.'/',$ext,$count, $data);      
    elseif (is_file($base.$value)) :   
    if (strpos($value, $ext)){     
      $data[] = $base.$value; 
      $count++;
    }     
    endif;
    endforeach; 
  return $data; 
}

echo '<b>'.count(werks(dirname(__FILE__).'/album/',".tb")).'</b>';

?>
