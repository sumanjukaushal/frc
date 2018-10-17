<script>
    function hideAllCat(catID){
        var catAppend = "ait-item-category-";
        var checkboxAppend = 'in-ait-item-category-';
        <?php
        $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1, 'hide_empty' => 0));
        
        $freeCat = array(9 => 'Free Camps',6 => 'Dump Points', 30 => 'Rest Areas', 'Tourist Information Centres', 'Tourist Information Outlets');        
        $businessCat = array(
                                'camping-accessories', //5
                                'food-wine',           //8
                                'information-centre',  //2088
                                'entertainment',       //7    
                                'fuel',                //210
                                'groceries',           //10
                                'markets',             //106
                                'medical',             //15
                                'personal-health',     //17
                                'repairs',             //4
                                'services',            //107
                                'accommodation',       //2096
                                //'auto-rv-caravan',
                                //'iga-stores',
                                //'things-to-do',
                                //'united-fuels',
                                //'puma',
                            );//'free-range-camping'
        
        $lowCostCat = array('campgrounds',);//'low-cost',
        $houseSittingCat = array('house-sitting');
        $parkOverCat = array('park-overs');
        $helpOutCat = array('help-out');
        $dollar25PlusCat = array('caravan-parks');//'25-per-night'
        
        $showDivs = "\n\t";
        $hideDivs = "\n\t";
        foreach($Itemcats as $sngCat){
            //echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').hide()"; //minified
            $showDivs.="jQuery('#'+catAppend+'".$sngCat->cat_ID."').show();";
            $showDivs.="jQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show();";
        }
        //echo'<pre>';print_r($Itemcats);die;
        foreach($Itemcats as $sngCat){
            //echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').hide()"; //minified
            //$hideDivs.="\n\tjQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').hide()";
            $hideDivs.="jQuery('#'+catAppend+'".$sngCat->cat_ID."').hide();";
            
            
            //get free cat parent id
            if(in_array($sngCat->name,$freeCat)){
                $freeCatParents[] = $sngCat->cat_ID;
            }
            //alert($freeCatParents);
            //get business listing parent ids
            if(in_array($sngCat->slug,$businessCat)){
                $businessCatParents[] = $sngCat->cat_ID;
            }
            
            //get low cost (Campgrounds) parent ids
            if(in_array($sngCat->slug,$lowCostCat)){
                $lowCostParents[] = $sngCat->cat_ID;
            }
            
            //get house sitting parent ids
            if(in_array($sngCat->slug,$houseSittingCat)){
                $houseSittingParents[] = $sngCat->cat_ID;
            }
            
            //get park over parent ids
            if(in_array($sngCat->slug,$parkOverCat)){
                $parkOverParents[] = $sngCat->cat_ID;
            }
            
            //get help out parent ids
            if(in_array($sngCat->slug,$helpOutCat)){
                $helpOutParents[] = $sngCat->cat_ID;
            }
            
            //get park dollar 25+ cats parent ids
            if(in_array($sngCat->slug,$dollar25PlusCat)){
                $dollar25Plus[] = $sngCat->cat_ID;
            }
        }
        ?>
    }
    jQuery( "#directory_1" ).click(function() {
        var catAppend = "ait-dir-item-category-";
        var checkboxAppend = 'in-ait-dir-item-category-';
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n\n\t";
            foreach($freeCatParents as $catID){
                echo "jQuery('#'+catAppend+'".$catID."').show();"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-dir-item-category', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));
                foreach($Itemcats as $sngCat){
                    echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').show();"; //minified
                    echo "jQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show();"; //minified
                }
            }
        ?>
    });
    
    //show business categories
    jQuery( "#directory_2" ).click(function() {
        console.log('buisness');var catAppend = "ait-items-";var checkboxAppend = 'in-ait-items-';
        
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n\n\t";
            foreach($businessCatParents as $catID){
                echo "jQuery('#'+catAppend+'".$catID."').show();"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));                //echo "\n\t/*cat id: $catID */\n";
                foreach($Itemcats as $sngCat){
                    echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').show();"; //minified
                    echo "jQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show();"; //minified
                }
                //echo "\n\t/*End cat id: $catID */\n";
            }
        ?>            
    });
    
    //low cost categories ('Campgrounds');
    jQuery( "#directory_3" ).click(function() {
        //alert('low cost');
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        //in-popular-ait-item-category
        //hideAllCat('directory_3');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n\n\t";
            foreach($lowCostParents as $catID){
                echo "jQuery('#'+catAppend+'".$catID."').show();"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').show();"; //minified
                    echo "jQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show();"; //minified
                }
            }
        ?>            
    });
    
    //house sitting categories
    jQuery( "#directory_4" ).click(function() {
        //alert('house sitting');
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        //in-popular-ait-item-category
        //hideAllCat('directory_4');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($houseSittingParents as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "\n\tjQuery('#'+catAppend+'".$sngCat->cat_ID."').show()"; //minified
                    echo "\n\tjQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show()"; //minified
                }
            }
        ?>            
    });
    
    //Park Over categories
    jQuery( "#directory_5" ).click(function() {
        //alert('Park Over');
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        //in-popular-ait-item-category
        //hideAllCat('directory_5');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($parkOverParents as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "\n\tjQuery('#'+catAppend+'".$sngCat->cat_ID."').show()"; //minified
                    echo "\n\tjQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show()"; //minified
                }
            }
        ?>            
    });
    
    //Park Over categories
    jQuery( "#directory_6" ).click(function() {
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        //in-popular-ait-item-category
        //hideAllCat('directory_6');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($helpOutParents as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "\n\tjQuery('#'+catAppend+'".$sngCat->cat_ID."').show()"; //minified
                    echo "\n\tjQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show()"; //minified
                }
            }
        ?>            
    });
    
    //Dollar 25+ categories
    jQuery( "#directory_7" ).click(function() {
        var catAppend = "ait-items-";var checkboxAppend = 'in-ait-items-';
        //in-popular-ait-item-category
        //hideAllCat('directory_7');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($dollar25Plus as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "\n\tjQuery('#'+catAppend+'".$sngCat->cat_ID."').show()"; //minified
                    echo "\n\tjQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show()"; //minified
                }
            }
        ?>            
    });
</script>