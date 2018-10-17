<html>
    <head>
        <title>Script file code generating Utility</title> 
    </head>
    <body>
<?php
    require('features-array.php');
    $strFile = "<?php\n\t\$featureArr = array(";
    $str2File = $str3File = $str4File = "";
    $str_5_part_1 = $str_5_part_2 = $str_5_part_3 = $str6File_1 = $str_6_part_1 = $str_6_part_2 = "";
    
    foreach($features as $key => $featureArr){
        $srNo = $key+1;
        $feature = $featureArr[0];
        $tooltip = $featureArr[1];
        $imageName = $featureArr[2];
        //Generating file 1
        $strFile .= "\n\t\t\t\t\t\t$srNo\t=>\tarray('$feature', '$tooltip'),";
        
        //Generating file 2
        $str2File .= "\n\t\t\t\t\t\t(!empty(\$options['$feature'])) ||";
        
        //Generating file 3
        $str3File .= <<<STR_3_FILE
        \n\n\tif( dataRaw[key].optionsDir.hasOwnProperty("$feature") &&
    ( dataRaw[key].optionsDir['{$feature}']['enable'] == "enable" || dataRaw[key].optionsDir['{$feature}'] == 'Y')){
        {var \$imageSrc = "\$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/{$imageName}";}
        imagesContent = imagesContent + '<img width="30" height="30" src="{\$imageSrc}" title = "{__ '{$tooltip}'}" alt = "{__ '{$tooltip}'}" />';\n\t\t}
STR_3_FILE;
        
        //Generating file 4
        $str4File .= <<<STR_4_FILE
         \n\t\t//Feature $srNo
        if(array_key_exists('{$feature}', \$itemData) && (\$itemData['{$feature}']['enable'] == 'enable' || strtolower(\$itemData['{$feature}']) == 'y')){
             \$features[] = array('{$feature}',(string)\$itemData['{$feature}_addnl']);
        }elseif(array_key_exists('{$feature}_addnl', \$itemData) && trim(\$itemData['{$feature}_addnl']) != ""){
             \$features[] = array('{$feature}',(string)\$itemData['{$feature}_addnl']);
        }
STR_4_FILE;

        //Generating file 5 >>> 1st Block
        $str_5_part_1 .= <<<STR_5_FILE_1
            \n\t\t(!empty(\$item->optionsDir['{$feature}'])) ||
STR_5_FILE_1;

        //2nd Block
        $str_5_part_2 = $strFile;
        
        //3rd Block
        $str_5_part_3 .= <<<STR5FILE_3
        \n\n\t{if (!empty(\$item->optionsDir['{$feature}']))}				
            {var \$imageSrc = "\$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/{$imageName}";}
            imagesContent = imagesContent + '<img width="30" height="30" src={\$imageSrc} title = {__ '{$tooltip}'} alt = {__ '{$tooltip}'} />';
        {/if}
STR5FILE_3;

        //Generating file 6 >>> 1st Block
        $str_6_part_1 .= <<<STR_6_FILE_1
        \n\t(!empty(\$options['{$feature}'])) ||
STR_6_FILE_1;

        $str_6_part_2 .= <<<STR6FILE_2
    \n\t\t{if (!empty(\$options['{$feature}']))}				
        \t{var \$imageSrc = "{\$imagesURL}{$imageName}";}
        \timagesContent = imagesContent + '<img width="30" height="30" src={\$imageSrc} title = {__ '{$tooltip}'} alt = {__ '{$tooltip}'} />';
		{/if}
		//--------{$srNo}---------------\n
STR6FILE_2;
    }
    
    //Generating file 1
    $strFile .= "\n\t\t\t\t\t);\n?>";
    $file = fopen("4_single_dir_item.php","w");
    fwrite($file, $strFile);
    fclose($file);
    
    //Generating file 2
    $file2 = fopen("if_cond.txt", "w");
    fwrite($file2, $str2File);
    fclose($file2);
    
    //Generating file 3
    $file3 = fopen("4_ajaxfunctions-javascript.txt", "w");
    fwrite($file3, $str3File);
    fclose($file3);
    
    //Generating file 4
    $file4 = fopen("4_camp_function.txt", "w");
    fwrite($file4, $str4File);
    fclose($file4);
    
    //Generating file 5
    $file5 = fopen("4_map-javascript.txt", "w");
    fwrite($file5, $str_5_part_1);
    fwrite($file5,"\n\n\n".$str_5_part_2);
    fwrite($file5,"\n\n\n".$str_5_part_3);
    fclose($file5);
    
    //Generating file 6
    $file6 = fopen("4_map-single-javascript.txt", "w");
    fwrite($file6, $str_6_part_1);
    fwrite($file6,"\n\n\n".$str_6_part_2);
    fclose($file6);
    
    die('completed');
?>