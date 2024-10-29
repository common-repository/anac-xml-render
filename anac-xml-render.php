<?php
/*
Plugin Name: ANAC XML Render
Plugin URI: https://wordpress.org/plugins/anac-xml-render/
Description: Visualizzatore per file XML ANAC
Author: Enzo Costantini (SoftCos)
Version: 1.5.7
Author URI: www.softcos.eu
*/
require_once(plugin_dir_path(__FILE__) . 'anac-xml-admin.php');

function plugin_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}

function insertAbstract($data){
    echo '<h3>' . $data->metadata->entePubblicatore . '</h3>
          <div class="abstract">
          Anno di riferimento: &emsp;<b>' . $data->metadata->annoRiferimento . '</b> <br> 
          Ultima modifica: &emsp;<b>' . date_i18n("d F Y", strtotime($data->metadata->dataUltimoAggiornamentoDataset)) . ' </b><br>
          Percorso del file: &emsp;<a href="' . $data->metadata->urlFile . '" download="' . basename($data->metadata->urlFile) . '">' . $data->metadata->urlFile . '</a><br>
          </div>';
}

function insertHeader(){
    echo '<thead>
            <tr class="headerBar">
              <th style="width: 12%;">CIG</th>      
              <th style="width: 50%;">Oggetto<br>Dettaglio gara</th>
              <th style="width: 12%;">Data inizio<br>Data fine</th>
              <th class="number" style="width: 13%;" class="currency">Importo<br />aggiudicazione (&euro;)</th>
              <th class="number" style="width: 13%;" class="currency">Importo<br />liquidato (&euro;)</th>
            </tr>
          </thead>';
}

function createDetail_td($lotto, $count){
           
    $detail = '<div class="detail" id="detail' . $count . '" style="display: none;">
               <b>Procedura:</b><span id="Procedura"> ' . ucfirst(strtolower($lotto->sceltaContraente)) . '</span><br><b>Partecipanti:</b><br><span id="Partecipanti">';
              
    foreach ($lotto->partecipanti->partecipante as $partecipante) {
      $detail .= $partecipante->ragioneSociale . ' - ' . $partecipante->codiceFiscale . '<br>';
    }
      $detail .= '</span><b>Aggiudicatari:</b><br><span id="Aggiudicatari">';
    foreach ($lotto->aggiudicatari->aggiudicatario as $aggiudicatario) {
      $detail .= $aggiudicatario->ragioneSociale . ' - ' . $aggiudicatario->codiceFiscale . '</span><br>';
    }
     $detail .=  '</span></div>';
     
    return $detail;
}

function testData($data){
		if(!ereg("^[0-9]{4}-[0-9]{2}-[0-9]{2}$", $data)){
		return false;
	}else{
		$arrayData = explode("-", $data);
		$Giorno = $arrayData[2];
		$Mese = $arrayData[1];
		$Anno = $arrayData[0];
		if(!checkdate($Mese, $Giorno, $Anno)){
			return false;
		}else{
			return true;
		}
 	}
}

function getData($data) {
  $d = 'n/d';
  if(testData($data)){
        $d = date_i18n("d/m/Y", strtotime($data));
      }
  return $d;
}

function curl_file_exists($xml_url){
  $ch = curl_init($xml_url);
  curl_setopt($ch, CURLOPT_NOBODY, true);
  curl_exec($ch);
  $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  return $retcode == 200;
}
function sc_anac_xml_render($xml_url, $highlight) {
    if ($xml_url == null) {
        return false;
    }
// Individuazione della posizione del file    
    $fileExists = false;
    $my_host = $_SERVER['HTTP_HOST'];
    //$file_host = parse_url(stripslashes($xml_url));
    //Errore file non trovato
    $file_host = parse_url(stripslashes($xml_url), PHP_URL_HOST);
    if ($my_host == $file_host){
      $xml_file = $_SERVER['DOCUMENT_ROOT'] . parse_url(stripslashes($xml_url), PHP_URL_PATH);
      $fileExists = file_exists($xml_file); 
      if ($fileExists) {
        $data = new SimpleXMLElement($xml_file, NULL, TRUE);
      }
    } else {
      $fileExists = curl_file_exists($xml_url); 
      if ($fileExists) {
        $data = new SimpleXMLElement(file_get_contents(stripslashes($xml_url)));
      }
    }
    if(!$fileExists){
      echo '<div class="abstract">
            <h3>Il file XML richiesto non &egrave; disponibile</h3>' .
            ' URL: ' . $xml_url .
            '</div>'; 
      return false;
    } 
    insertAbstract($data);
    
    echo
    '<table id="xml_dataset" class="anac_xml">';
    insertHeader();
    echo
    '<tbody>';
    $count = 0;
    foreach ($data->xpath('//lotto') as $lotto) {

      $detail = createDetail_td($lotto, $count);
      echo '<tr>
              <td id="' . $count . '" class="expand down"  onclick="changeShow(this);">' . $lotto->cig . '</td>
              <td><span id="Oggetto">' . ucfirst($lotto->oggetto) . '</span>' . $detail . '</td>
              <td><span id="DataInizio">' . getData($lotto->tempiCompletamento->dataInizio) . '</span><br><span id="DataFine">' . getData($lotto->tempiCompletamento->dataUltimazione) . '</span></td>  
              <td class="bold number">' . number_format((double)$lotto->importoAggiudicazione, 2, ',', ' ') . '</td>
              <td class="bold number">' . number_format((double)$lotto->importoSommeLiquidate, 2, ',', ' ') . '</td>
            </tr>';
      $count++;      

    }
     
    echo '</tbody>
          </table>';      
    echo '<div class="clear"></div>
          <input type="hidden" id="xml_file" name="xml_file" value="' . base64_encode($data->asXML()) . '">';
    return true;
}
// Descrizione parametri
// anac_xml_render   = handle
// xml_url           = URL completo del file XML (anche se si trova su altro server)
// highlight [0/1]   = Se impostato evidenzia i lotti che presentano uno sbilancio tra aggiudicato e liquidato (default 0) 
// items_per_page    = Numero di righi per pagina (non impostato o se uguale a 0 viene mostrata la tabella completa con un campo di testo per la ricerca a testo libero -  default 10) 
        
// [anac_xml_render xml_url="<Url completo del file xml>" highlight=<1|0> items_per_page=<numero di lotti per pagina - default 10>] 
function anac_xml_render_shortcode( $atts ) {
  
  wp_enqueue_style( 'anac_xml_style',  plugins_url( '/anac-xml-render.css' , __FILE__ ));  
  
  
  extract( shortcode_atts(
    array(
      'xml_url' => '',
      'highlight' => 0,
      'items_per_page' => 10,
    ), $atts )
  );
   
  if(!sc_anac_xml_render($xml_url, $highlight)){
    return false;
  }
  
  if($items_per_page < 1){
    $items_per_page = 10;
  }
   
  echo 
     '<script type="text/javascript" src="' . plugins_url( '/js/softcos.table.js' , __FILE__ ) . '" ></script>
      <script type="text/javascript" src="' . plugins_url( '/js/base64code.js' , __FILE__ ) . '" ></script>
      <script type="text/javascript" src="' . plugins_url( '/js/xml.exporter.js' , __FILE__ ) . '" ></script>
      <script type="text/javascript">
  
         var tblNav = new tableNavigate("tblNav", "xml_dataset", ' . $items_per_page . ');
         tblNav.columnTypes = [{a:"COUNT",t:"INT"},{},{},{a:"SUM",t:"CURR"},{a:"SUM",t:"CURR"}];
         tblNav.init();
         
         function ExpFromXML(Sender, type){
           if(document.getElementById("xml_file")){
             return xmlExport(Sender, document.getElementById("xml_file").value, type, tblNav.filteredRows);
           } 
         }           
         
         function changeShow(obj){
           var i = obj.id; 
           if (obj.className == "expand down"){
             obj.className = "expand up";
             document.getElementById("detail" + i).style.display = "";
           } else {
             obj.className = "expand down";
             document.getElementById("detail" + i).style.display = "none";
           }
         } 
      </script>';
}
add_shortcode( 'anac_xml_render', 'anac_xml_render_shortcode' );
//******************************************************************************
function extract_xmlData($xml_file){
   $data = new SimpleXMLElement($xml_file, NULL, TRUE);
   return '<td style="text-align: center;">' . $data->metadata->annoRiferimento . '</td>
           <td style="text-align: center;">' . gmdate("d/m/Y", strtotime($data->metadata->dataUltimoAggiornamentoDataset)) . '</td>';
}
function prepareRows($path_url){
  $path_file = $_SERVER['DOCUMENT_ROOT'] . parse_url(stripslashes($path_url), PHP_URL_PATH);
  $img1 = plugins_url( '/img/cloud-arrow-down-32.png' , __FILE__ );
  $img2 = plugins_url( '/img/cloud-magnifier-32.png' , __FILE__ );
  $files = array(); 
  $handle = opendir($path_file);
  while (($file = readdir($handle))!==false) { $files[] = $file; }
  closedir($handle);
  sort($files); 
  $list = '';
  $index = 0;
  foreach($files as $file){
    if(pathinfo($file, PATHINFO_EXTENSION) == 'xml'){ 
      $list .= '<tr>
                  <td style="padding-left: 1.0em;">' . get_file_name($file) . '</td>' .
                  extract_xmlData($path_file . '/' . $file) . '
                  <td style="text-align: right;">' . get_file_size($path_file . '/' . $file) . '</td>
                  <td style="text-align: center;">
                    <a href="' . $path_url . '/' . $file. '" download="' . $file . '" title="Scarica il file: '.$file.'" >
                      <img src="' . $img1 . '" alt="Download"/>
                    </a>
                  </td>                                 
                  <td style="text-align: center;">
                                       
                    <form method="POST" id="file_list' . $index . '" name="file_list' . $index . '" target="_self" enctype="multipart/form-data">
                      <input type="hidden" id="xml_url" name="xml_url" value="' . $path_url . '/' . $file . '">                   
                    <a href="javascript:;" onclick="document.forms[\'file_list' . $index . '\'].submit();" title="Visualizza il file: '.$file.'" >
                      <img src="' . $img2 . '" alt="Download"/>
                    </a>
                    
                    </form>
                 
                  </td>
                </tr>'; 
    $index++;
    }  
  }
  return $list;
}
function anac_xml_create_page($path_url) {
  if(isset($_POST['xml_url'])){
     
     $url = $_POST['xml_url'];
     echo '<div class="back_2_file_list">
             <a href="' . $_SERVER["HTTP_REFERER"] . '" title="Torna all\'elenco dei file XML">
              <img class="light_img" src="' . plugins_url( '/img/arrow_left.png' , __FILE__ ) . '" alt="Indietro" title="Torna all\'indice dei file">
             </a>
           </div>';
     
     echo do_shortcode( '[anac_xml_render xml_url="' . $url . '"]');  
     return;
  }else{
    if(!$path_url){ 
      return false; 
    }  
       
    echo '<div class="file_list_title"><br>
            <h2>Autorit&agrave; Nazionale Anti Corruzione</h2>
            <p>
              Adempimenti Legge 190/2012 art. 1, comma 32<br>
              Pubblicazione dei file Xml per esercizio finanziario
            </p>
          </div>
          <table class="anac_xml file_list">
          <thead>
            <tr>                                                                    
              <th style="width: 31%;">documento</th>  
              <th style="width: 15%; text-align: center;">anno</th>                  
              <th style="width: 15%; text-align: center;">data</th>   
              <th style="width: 15%; text-align: right;">peso</th> 
              <th style="width: 12%; text-align: center;">scarica</th>
              <th style="width: 12%; text-align: center;">visualizza</th>
              </tr>
            </tr>
          </thead>                                                          
          <tbody>';     
    
    echo prepareRows($path_url);
    
    echo '</tbody>
          <tfoot>
          <th id="exportBar" colspan="6">&emsp;</th>
          </tfoot>
          </table>';
          
    
    echo '<div class"clear"></div>';           
  } 
}
function anac_xml_file_list_shortcode( $atts ) {
	
  wp_enqueue_style( 'anac_xml_style',  plugins_url( '/anac-xml-render.css' , __FILE__ )); 
  
  extract( shortcode_atts(
		array(
			'path_url' => '',
		), $atts )
	);
  anac_xml_create_page($path_url);
}
add_shortcode( 'anac_xml_file_list', 'anac_xml_file_list_shortcode' );
?>