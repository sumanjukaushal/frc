<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    $postTble = $wpdb->prefix.'posts';
    $postmetaTble = $wpdb->prefix.'postmeta';
    $userTble = $wpdb->prefix.'users';
    $termsTble = $wpdb->prefix.'terms';
    $termTaxnomyTble = $wpdb->prefix.'term_taxonomy';
    $termRelationtble = $wpdb->prefix.'term_relationships';
    $isOldTheme = is_old_theme();
    $postType =  $isOldTheme ? 'ait-dir-item' : 'ait-item';
    $taxonomyCategory =  $isOldTheme ? 'ait-dir-item-category' : 'ait-items';
    $taxonomyLocations =  $isOldTheme ? 'ait-dir-item-location' : 'ait-locations';
    $yoastCategory =  $isOldTheme ? '_yoast_wpseo_primary_ait-dir-item-category' : '_yoast_wpseo_primary_ait-items';
    $yoastLocations =  $isOldTheme ? 'yoast_wpseo_primary_ait-dir-item-location' : 'yoast_wpseo_primary_ait-locations';
    $postQuery = $wpdb->get_results("SELECT * FROM `$postTble` WHERE `post_type` = '$postType' order by `ID` DESC");
    $finalCsvArr = array();
    //echo $siteURL = site_url();
    foreach($postQuery as $key => $posts){
        $term_data = $termTaxonomyId = $cat_loc_of_term = $termsDatas = array();
        $postId = $posts->ID;
        $postTitle = $posts->post_title;
        $postStatus = $posts->post_status;
        $postDate = date('d M Y',strtotime($posts->post_date));
        $postName = $posts->post_name;
        if(!empty($postName)){
            $permalink = "{$siteURL}/item/".$postName.'/';
        }else{
            $permalink = '';
        }
        $authorId = $posts->post_author;
        $userQuery = $wpdb->get_results("SELECT * FROM `$userTble` WHERE `ID`=$authorId");
        $userLogin = $userQuery[0]->user_login;
        $userEmail = $userQuery[0]->user_email;
        $postmetaQuery = $wpdb->get_results("SELECT `meta_key`,`meta_value`,`meta_id` FROM `$postmetaTble` WHERE `post_id`=$postId AND `meta_key` IN('$yoastCategory', '$yoastLocations', '$postType')");
        $postmetaData = unserialize($postmetaQuery[0]->meta_value);
        
        if( is_array($postmetaData) && array_key_exists('telephone',$postmetaData)){
            $telephone = $postmetaData['telephone'];    
        }else{
            $telephone = '';
        }
        
        if( is_array($postmetaData) && array_key_exists('email',$postmetaData)){
            $email = $postmetaData['email'];    
        }else{
            $email = '';
        }
        
        $termRelQuery = $wpdb->get_results("SELECT * FROM `$termRelationtble` WHERE `object_id`=$postId");
        foreach($termRelQuery as $key => $terms){
            $termTaxonomyId[] = $terms->term_taxonomy_id;    
        }
        $termTaxonomyId_str = '';
        if(!empty($termTaxonomyId)){
            $termTaxonomyId_str = implode(',',$termTaxonomyId);
        }
        if(!empty($termTaxonomyId_str)){
            $taxonomyQuery = $wpdb->get_results("SELECT * FROM `$termTaxnomyTble` WHERE `term_taxonomy_id` IN($termTaxonomyId_str)");
            
            foreach($taxonomyQuery as $key => $termData){
                if($termData->taxonomy == $taxonomyCategory || $termData->taxonomy == $taxonomyLocations){
                    $term_data[$termData->term_id] = $termData->taxonomy;
                }
            }
        }
        
        if(!empty($term_data)){
            $term_ids = implode(',',array_keys($term_data));
        }else{
            $term_ids = '';
        }
        
        if(!empty($term_ids)){
            $termQuery = $wpdb->get_results("SELECT * FROM `$termsTble` WHERE `term_id` IN($term_ids)");
            
            foreach($termQuery as $key => $termsData){
                $termsDatas[$termsData->term_id] = $termsData->name;
            }
        }
        
        if(is_array($term_data)){
            foreach($term_data as $term_id => $term_name){
                if(array_key_exists($term_id,$termsDatas)){
                    foreach($termsDatas as $terms_id => $terms_name){
                        if($terms_id == $term_id){
                            if($term_name == $taxonomyCategory){
                                $cat_loc_of_term['cat'][$term_id] =$terms_name;
                            }
                            if($term_name == $taxonomyLocations){
                                $cat_loc_of_term['loc'][$term_id] =$terms_name;
                            }
                        }
                    }
                }
            }
        }
        
        if(!empty($cat_loc_of_term)){
            if(array_key_exists('cat',$cat_loc_of_term)){
                $categories = implode('|',$cat_loc_of_term['cat']);
            }else{
                $categories = '';
            }
            if(array_key_exists('loc',$cat_loc_of_term)){
                $locations = implode('|',$cat_loc_of_term['loc']);
            }else{
                $locations = '';
            }
        }else{
            $categories = '';
            $locations = '';
        }
        $finalCsvArr[] = array(
                               'Post ID'=>$postId,
                               'Title'=>$postTitle,
                               'Date'=>$postDate,
                               'Status'=>$postStatus,
                               'Permalink'=>$permalink,
                               'Item Categories'=>$categories,
                               'Item Locations'=>$locations,
                               'Author Login'=>$userLogin,
                               'Author Email'=>$userEmail,
                               'Telephone'=>$telephone,
                               'Email'=>$email,
                               );
    }
    $timestamp = strtotime(date('d M Y'));
    $fileName = "export_$timestamp.csv";
    //header('Content-Type: application/csv');
    header('Content-Type: text/csv; charset=utf-8');  
    header('Content-Disposition: attachment;filename='."$fileName");
    if(isset($finalCsvArr['0'])){
        $fp = fopen('php://output', 'w');
        //$fp = fopen($fileName, 'w');
        fputcsv($fp, array_keys($finalCsvArr['0']));
        $count = 0;
        foreach($finalCsvArr AS $values){
            $count ++;    
            fputcsv($fp, $values);
        }
        fclose($fp);die;
    }
    //echo'<pre>';print_r($finalCsvArr);
?>