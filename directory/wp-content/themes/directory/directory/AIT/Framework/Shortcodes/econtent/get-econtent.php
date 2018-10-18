<?php
  $result = "No content founds";
  
  /* GET FILES */
  $examples = array();
  $examplesDir = realpath(getcwd().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."design".DIRECTORY_SEPARATOR."example-content");
  if(is_dir($examplesDir) && file_exists($examplesDir)){
    $dirHandler = opendir($examplesDir);
    while($exampleFile = readdir($dirHandler)){
      $systemFile = pathinfo($examplesDir.DIRECTORY_SEPARATOR.$exampleFile);
      if($exampleFile != "." && $exampleFile != ".." && $exampleFile != ".svn" && $systemFile['extension'] == 'html' && $exampleFile != ".htaccess"){
        array_push($examples, $exampleFile);
      }
    }
    closedir($dirHandler);
  } else {
    $result .= "No examples found";
  }
  /* GET FILES */
  
  /* GET CONTENT */
  foreach($examples as $example){
    $fName = explode('-',$example);
    $exampleType = str_replace(".html",'',$fName[3]);
    if($exampleType == $_REQUEST['type']){
      /* REMOVE COMMENTS */
      //preg_replace("!/\*.*?\*/!s",file_get_contents($examplesDir.'/'.$example),$result);
      /* REMOVE COMMNETS */
      $result = file_get_contents($examplesDir.'/'.$example);
    }
  }
  /* GET CONTENT */
  
  echo $result;
?>