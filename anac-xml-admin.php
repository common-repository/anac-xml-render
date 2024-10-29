<?php
require_once(plugin_dir_path(__FILE__) . 'file_utils.php'); 
function testata(){
?>
<link rel="stylesheet" href="<?php echo plugins_url( '/anac-xml-admin.css' , __FILE__ ); ?>" type="text/css" media="all" />
<div class="wrap">            
  <table class="title" width="100%">  
    <tbody>  
      <tr>    
        <td width="320px">      
          <img src="<?php echo plugins_url( '/img/anac_small.jpg' , __FILE__ ); ?>" title="Anac Xml Render">    
		</td>    
		<td>      
          <h2 class="title"><strong>ANAC XML Render</strong>
            <br>       
            <span style="font-size: small;">Gestione dei file XML
              <br />
              <em>di Enzo Costantini (SoftCos)</em>
            </span>
            <br>      
		  </h2>    
		</td>  
      </tr>  
    </tbody>  
  </table>    
  <hr>  
<?php  
}
function anac_xml_upload_form()
{
  if ( !current_user_can( 'manage_categories' ) )  {
		wp_die('Non hai privilegi sufficienti per accedere a questa sezione...');
	}
  
     
  $def_folder = get_option('anac-xml-render_def_folder', '');
  if ($def_folder != '') {
    $path = preg_replace('#/+#', '/', $_SERVER['DOCUMENT_ROOT'] . parse_url($def_folder, PHP_URL_PATH));
    if (!is_dir($path)) {
      mkdir($path, 0755, true);
    } else if (!is_readable($path) || !is_writable($path)) {
      chmod($path, 0755);
    }
  } else {
    $res_msg = 'Non &egrave; stata impostata la cartella di default per il caricamento dei file XML!<br>
                Dal menu <strong>Impostazioni</strong> inserire il dato e continuare...';
    echo '<div id="message" class="error"><p>' . $res_msg . '</p></div>';
    return;
  } 
  require(plugin_dir_path(__FILE__) . 'upload_file.php'); 
  
  $files = array(); 
  $handle = opendir($path);
  while (($file = readdir($handle))!==false) { $files[] = $file; }
  closedir($handle);
  sort($files); 
  $list = '';
  foreach($files as $file){
    if(pathinfo($file, PATHINFO_EXTENSION) == 'xml'){ 
      $list .= '<tr onclick="document.getElementById(\'Anac_Url\').innerHTML = \'' . $def_folder . get_file_name($file) . '\';">
                <td style="padding-left: 1.0em;">' . get_file_name($file) . '</td>
                <td style="text-align: center;">' . get_file_time($path . '/' . $file) . '</td>
                <td style="text-align: right; padding-right: 1.0em;">' . get_file_size($path . '/' . $file) . '</td>
                </tr>';
    }  
  }      
  ?>      
  <div class="wrap">          
<?php testata(); 
    if($uploadOk == 0){
      echo '<div id="message" class="error"><p>' . $res_msg . '</p></div>';
    } else if($uploadOk == 1) {
      echo '<div id="message" class="updated"><p>' . $res_msg . '</p></div>';
    }   
      ?>             
    <fieldset class="form_fieldset">
      <legend>Elenco dei file sul server
      </legend>     
      <span>Cartella preimpostata:      
        <span style="color: rgb(50,50,150); font-size: 1.25em; font-weight: bold;">      
          <?php echo $def_folder; ?>    
        </span>  
      </span>       
      <br>  
      <br>       
      <div class="listbox" id="file_list">            
        <table class="filebox" cellspacing="0px">                 
          <thead>                     
            <tr>                          
              <th style="width: 45%">nome del file           
              </th>                        
              <th style="width: 30%">data           
              </th>                        
              <th style="width: 25%; text-align: right; padding-right: 1.0em;">dimensione           
              </th>                     
            </tr>                 
          </thead>                 
          <tbody>                       
            <?php echo $list ?>                  
          </tbody>            
        </table>       
      </div>        
      <br>  
      <label>URL da comunicare all'ANAC
      </label>  
      <div class="white_div">    
        <span class="anacurl" id="Anac_Url">    selezionare un rigo nella tabella...     
        </span>  
      </div>       
    </fieldset>  
    <br>       
    <form method="post" id="upload_form" enctype="multipart/form-data" target="_self">                           
      <fieldset class="form_fieldset">
        <legend>Modulo per il caricamento dei file XML
        </legend>    
        <div class="white_div">                 
          <input class="file_upload" type="file" id="fileToUpload" name="fileToUpload" />                
        </div>               
        <input type="checkbox" id="fileToOverwrite" name="fileToOverwrite" value="true"> Sovrascrivere il file se gi&agrave; esistente                   
        <br>
        <br>                
        <button class="form_button" id="submit" name="submit" onclick="document.getElementById('upload_form').submit();">Carica il file     
        </button>&emsp;                
        <button class="form_button" id="reset"  name="reset"  onclick="document.getElementById('upload_form').reset();">Svuota     
        </button>                  
        </p>      
      </fieldset>      
    </form>          
  </div>          
<?php
}
function anac_xml_options_form(){
  if ( !current_user_can( 'manage_categories' ) )  {
		wp_die('Non hai privilegi sufficienti per accedere a questa sezione...');
  } else {
    require(plugin_dir_path(__FILE__) . 'options_save.php'); 
  }
  ?>     
  <div class="wrap">   
    <?php testata(); ?>          
    <form method="post" id="axr_option_form" enctype="multipart/form-data" target="_self">                           
      <fieldset class="form_fieldset">
        <legend>Impostazioni per i dataset XML
        </legend>    
        <br>    
        <label for="def_folder">Cartella predefinita per caricare i file XML
        </label>
        <br>               
        <input spellcheck="false" type="text" name="def_folder" id="def_folder" value="<?php echo get_option( 'anac-xml-render_def_folder', '' ); ?>" placeholder="Non impostato..."  class="input_field">    
        <input type="hidden" name="old_folder" id="old_folder" value="<?php echo get_option( 'anac-xml-render_def_folder', '' ); ?>">    
        <div class="help_div">      Impostare in questo campo l'URL della cartella predefinita in cui si intendono caricare i file XML
          <br>      Il percorso completo deve essere del tipo <strong>http://www.miosito.it/anac_xml/</strong>
          <br>      Successivamente sar&agrave; possibile caricare i file direttamente in tale cartella dal modulo di upload.
          <br>
          <br>      <strong>NB: Se gi&agrave; esiste una cartella contenente i file inserire il suo URL!</strong>    
        </div>    
        <p>&emsp;
        </p>    
        <label for="def_permalink">URL completo della pagina (o altro documento) in cui si vuole visualizzare l'elenco di tutti i file XML presenti
        </label>
        <br>               
        <input spellcheck="false" type="text" name="def_permalink" id="def_permalink" value="<?php echo get_option( 'anac-xml-render_def_permalink', '' ); ?>" placeholder="Non impostato..." class="input_field">    
        <input type="hidden" name="old_permalink" id="old_permalink" value="<?php echo get_option( 'anac-xml-render_def_permalink', '' ); ?>">    
        <div class="help_div">      In questo campo va opzionalmente inserito l'URL della pagina che mostra l'elenco completo dei file XML presenti nella cartella predefinita.
          <br>      Questo URL si pu&ograve; prendere quando si va in inserimento/modifica della pagina (o documento) attraverso l'apposito pulsante:       
          <div style="text-align: center;">
            <img align="top" src="<?php echo plugins_url( '/img/shortlink.png' , __FILE__ ); ?>">
          </div>
          <br>      <strong>NB: Questa seconda impostazione non &egrave; assolutamente indispensabile per il funzionamento del plugin!
            <br>      Essa serve solo a visualizzare il contenuto della cartella contenente i file XML in caso si digiti direttamente l'URL nel browser.</strong>         
        </div>    
        <p class="submit">
          <input type="submit" name="submit" id="submit" class="button button-primary" value="Salva le modifiche">
        </p>                
      </fieldset>      
    </form>                 
  </div>
<?php    
}
function anac_xml_info_form(){
  if ( !current_user_can( 'manage_categories' ) )  {
		wp_die('Non hai privilegi sufficienti per accedere a questa sezione...');
	}
  ?>     
  <div class="wrap">   
    <?php testata(''); ?>     
    <div style="padding:1em; border: 1px solid #AAAAAA; background: #FFFFFF;">            <h3>Uso</h3>    
      <hr><h4>Per visualizzare i dati di un file in una tabella</h4>          
      <em>Inserire in qualunque pagina o articolo dove si vuole visualizzare la tabella dei dati ANAC lo shortcode:     
      </em>
<pre>[<strong>anac_xml_render</strong>&emsp;<strong>xml_url</strong>="Url completo del file XML" &emsp;<strong>highlight</strong>=[0/1] items_per_page=## ]</pre><h5>Esempio 1</h5>
<pre>[<strong>anac_xml_render</strong>&emsp;<strong>xml_url</strong>="http://www.sito.it/avcp/2015.xml"]</pre>  Con questo codice vengono usate le impostazioni di default (
      <em>Nessuna evidenziazione e 10 lotti per pagina
      </em>) <h5>Esempio 2</h5>
<pre>[<strong>anac_xml_render</strong>&emsp;<strong>xml_url</strong>="http://www.sito.it/avcp/2015.xml" <strong>highlight</strong>=1 items_per_page=5]</pre>  Con questo codice viene:     
      <br>  - attivata l'evidenziazione dei lotti che presentano uno sbilancio tra aggiudicato e liquidato     
      <br>- attivata la visualizzazione di 5 lotti per pagina.     
      <br>    
      <hr>          <h4>Per visualizzare l'elenco dei file XML presenti nella cartella preimpostata</h4>          
      <em>Creare una nuova pagina o un documento della trasparenza e inserire shortcode:     
      </em>    
<pre>[<strong>anac_xml_file_list</strong>&emsp;<strong>path_url</strong>="Url completo della cartella in cui risiedono i file XML" ]</pre>   <h5>Esempio</h5>
<pre>[<strong>anac_xml_file_list</strong>&emsp;<strong>path_url</strong>="http://www.sito.it/avcp/"]</pre>   Nella pagina verr&agrave; visualizzata una tabella con l'elenco dei file Xml presenti nella cartella <strong>http://www.sito.it/avcp/</strong>
      <br>Da questa pagina sar&agrave; possibile scaricare o visualizzare i singoli dataset XML.      
    </div>          
  </div>
<?php
}
add_action( 'admin_menu', 'anac_xml_add_menu', 8.1 );
function anac_xml_add_menu() {
	$icon_url = plugins_url( '/img/file-xml.png' , __FILE__ );
	add_object_page(
		'Visualizzatore Xml Anac', 'Anac <b>Xml</b>', 
		'manage_categories', 
		'anac-xml-menu',
		'anac_xml_info_form' );
	add_submenu_page( 
		'anac-xml-menu',
		'Visualizzatore Xml Anac', 'Anac <b>Xml</b> info',
		'manage_categories', 
		'anac-xml-menu',
		'anac_xml_info_form' );
 
	add_submenu_page( 
		'anac-xml-menu',
		'Pannello di upload', 'Upload dei file XML',
		'manage_categories', 
		'anac-xml-upload-menu',
		'anac_xml_upload_form' );
  
    add_submenu_page( 
		'anac-xml-menu',
		'Impostazioni Xml Anac', 'Impostazioni',
		'manage_categories', 
		'anac-xml-options-menu',
		'anac_xml_options_form' );
}
  ?>