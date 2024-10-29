<?php
$uploadOk = -1;
if (isset($_POST['submit']) && ($_FILES["fileToUpload"]["name"] != '')) {
    
    $target_file = $path . basename($_FILES["fileToUpload"]["name"]);
    
    $uploadOk = 1;
    $fileType = pathinfo($target_file, PATHINFO_EXTENSION);
    
    $res_msg = '';
    
    $fileToOverwrite = isset($_POST['fileToOverwrite']) ? $_POST['fileToOverwrite'] : false;
    if(!$fileToOverwrite){
      if (file_exists($target_file)) {
          $res_msg .= "Il file esiste gi&agrave;. <br>";
          $uploadOk = 0;
      }
    }
    
    if ($_FILES["fileToUpload"]["size"] > 524288) {
        $res_msg .= "Il file supera le dimensioni consentite. <br>";
        $uploadOk = 0;    
    } 
    
    if ($fileType != "xml") {
        $res_msg .= "Sono ammessi solo file XML. <br>";
        $uploadOk = 0;
    }    
    
    if ($uploadOk == 0) {
        $res_msg = "<strong>Il file non &egrave; stato caricato. </strong><br>" . $res_msg;
        
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $res_msg = "Il file <strong>" . basename($_FILES["fileToUpload"]["name"]) . "</strong> &egrave; stato correttamente caricato.";
        } else {
            $res_msg = "<strong>Si sono verificati errori imprevisti nel caricamento del file!</strong>";
        }
    }
    
}
?>