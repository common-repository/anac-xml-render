<?php
define("ROW_LEN", 45);

function get_file_name($file){
  if(strlen($file) > ROW_LEN){
    return substr($file, 0, ROW_LEN - 5) . '&hellip;' . substr(strrchr($file,'.'),0); 
  } else {
    return $file;
  }
}

function get_file_time($file) 
{
  $stat = stat($file);
  $timestamp = $stat['mtime'];
  return gmdate("d/m/Y H:i", $timestamp);
}

function get_file_date($file) 
{
  $stat = stat($file);
  $timestamp = $stat['mtime'];
  return gmdate("d/m/Y", $timestamp);
}

function get_file_size($file) 
{
  $stat = stat($file);
  $bytes = $stat['size'];
	if ($bytes < 1024) $size = $bytes.'&nbsp;&nbsp;&nbsp;b';
	elseif ($bytes < 1048576) $size = round($bytes / 1024, 0).'&nbsp;kib';
	elseif ($bytes < 1073741824) $size = round($bytes / 1048576, 0).'&nbsp;mib';
	elseif ($bytes < 1099511627776) $size = round($bytes / 1073741824, 0).'&nbsp;gib';
	else $size = round($bytes / 1099511627776, 0).'&nbsp;tib';  
  return $size;
}
?>