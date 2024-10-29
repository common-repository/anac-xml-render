<?php
function index_file_create($def_path, $def_permalink){
  if (is_dir($def_path)) {
        if ($def_permalink != '') {
          $fileS = plugin_dir_path(__FILE__) . 'tpl/index.htm.tpl';
          $fileD = $def_path . 'index.htm';
          if (copy($fileS, $fileD)) {
            $html = file_get_contents($fileD);
            $html = str_replace('%shortlink%', $def_permalink, $html);
            file_put_contents($fileD, $html);
          }
        }
      }
}
if (isset($_POST['submit'])) {
  if (isset($_POST['def_folder']) && ($_POST['def_folder'] != $_POST['old_folder'])) {
    $def_folder = $_POST['def_folder'];
    if ($def_folder != '') {
      $def_path = preg_replace('#/+#', '/', $_SERVER['DOCUMENT_ROOT'].parse_url($def_folder, PHP_URL_PATH) . '/');
      if (!is_dir($def_path)) {
        mkdir($def_path, 0755, true);
      } else if (!is_readable($def_path) || !is_writable($def_path)) {
        chmod($def_path, 0755);
      }
      $def_permalink = get_option( 'anac-xml-render_def_permalink', '' );
      if ($def_permalink != '') {
        index_file_create($def_path, $def_permalink);
      }
    }
    if (substr($def_folder, -1, 1) !== '/'){
      $def_folder = $def_folder .'/'; 
    }
    update_option('anac-xml-render_def_folder', $def_folder);
  }
  if (isset($_POST['def_permalink']) && ($_POST['def_permalink'] != $_POST['old_permalink'])) {
    $def_permalink = $_POST['def_permalink'];
    $def_folder = get_option('anac-xml-render_def_folder', '');
    if ($def_folder != '') {
      $def_path = preg_replace('#/+#', '/', $_SERVER['DOCUMENT_ROOT'].parse_url($def_folder, PHP_URL_PATH));
      index_file_create($def_path, $def_permalink);
    }
  update_option('anac-xml-render_def_permalink', $def_permalink);
  }
}
?>