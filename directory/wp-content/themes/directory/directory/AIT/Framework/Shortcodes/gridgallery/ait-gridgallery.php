<?php
function ait_gridgallery( $params ) {
    extract( shortcode_atts( array (
        'slug' => 'all',
        'cols' => '5',
        'count' => '15',
        'category' => '0',
        'display' => 'detail',
        'showfilter' => true,
        'order' => 'chronological',
        'height' => 'auto',
        'descheight' => '50px',
        'description' => '0'
    ), $params ) );

  $categories = array();
  $portfolio_categories = get_categories( array( 'taxonomy' => 'ait-grid-portfolio-category', 'orderby' => 'menu_order', 'order' => 'ASC'));
  $portfolio_cats = array();
  if($category == '0'){
    $args = array( 'numberposts' => 500 , 'cat' => $category , 'post_type' => 'ait-grid-portfolio', 'orderby' => 'menu_order', 'order' => 'ASC', 'suppress_filters' => false );
    $portfolio_images = get_posts($args);
  } else {
    $categories = explode(", ",$category);
    $portfolio_images = array();
    foreach($categories as $cat){
      /* IMAGE RESOLVING */
      $args = array( 'post_type' => 'ait-grid-portfolio', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 500, 'tax_query' => array( array( 'taxonomy' => 'ait-grid-portfolio-category', 'field' => 'id', 'terms' => intval($cat)) ) );
      foreach(get_posts($args) as $cPost){
        $portfolio_images[$cPost->ID] = $cPost;
      }
      /* IMAGE RESOLVING */

      /* CATEGORY RESOLVING */
      foreach($portfolio_categories as $pCat){
        if($cat == $pCat->cat_ID){
          array_push($portfolio_cats,$pCat);
        }
      }
      /* CATEGORY RESOLVING */
    }
    $portfolio_categories = $portfolio_cats;
  }

  $result = '';

  $click = '';
  switch($display)
  {
    case 'detail':
      $click = 'item-detail';
    break;
    case 'direct':
      $click = 'item-direct';
    break;
    case 'fancybox':
      $click = 'item-fancybox';
    break;
  }

  if($portfolio_images){
    $style = "display: block; visibility: hidden";
    switch($cols){
      case 5:
        if($description){
          $result .= '<div class="portfolio clearfix five gridgallery '.$click.' with-description">';
        } else {
          $result .= '<div class="portfolio clearfix five gridgallery '.$click.'">';
        }
      break;
      case 4:
        if($description){
          $result .= '<div class="portfolio clearfix four gridgallery '.$click.' with-description">';
        } else {
          $result .= '<div class="portfolio clearfix four gridgallery '.$click.'">';
        }
      break;
      case 3:
        if($description){
          $result .= '<div class="portfolio clearfix three gridgallery '.$click.' with-description">';
        } else {
          $result .= '<div class="portfolio clearfix three gridgallery '.$click.'">';
        }
      break;
    }
    $result .= '<div id="portfolio-loader" style="position: absolute; top: 35%; left: 50%"><img src="'.THEME_IMG_URL.'/loader2.gif" width="50" height="50" alt="loader" title="" /></div>';

    if($showfilter){
      /* SORTING MENU */
      if(intval($category) > 1 || $category == 0){
        $result .= '<ul id="filterOptions" class="port-cat categories clearfix"  style="'.$style.'">';
        $result .= '<li class="active"><a href="#" class="all">All</a></li>';
        if($portfolio_categories){
          foreach($portfolio_categories as $category){
            $result .= '<li><a href="#" class="cat-'.$category->slug.'">'.$category->name.'</a></li>';
          }
        }
        $result .= '</ul>';
        $result .= '<a href="#" class="port-cat icon"  style="'.$style.'"><!-- This is ICON! --></a>';
      }
      /* SORTING MENU */
    }

    /* TILE COUNTING */
    $i = 0;
    $result .= '<div class="ulHolder" style="'.$style.'">';

    if($height == 'auto'){
      $result .= '<div id="gridgallery-setting-height" style="display: none">auto</div>';
    } else {
      $result .= '<div id="gridgallery-setting-height" style="display: none">'.$height."</div>";
    }

    $result .= '<div class="portfolioHolder"><ul class="ourHolder clearfix">';

    switch($order){
      case 'chronological':
      break;
      case 'newest':
      $portfolio_images = array_reverse($portfolio_images);
      break;
      case 'random':
      shuffle($portfolio_images);
      break;
    }

    foreach($portfolio_images as $counter=>$item){
      $itemCats = get_the_terms( $item->ID, 'ait-grid-portfolio-category' );
      $itemCategories = "";
      if($itemCats){
        foreach($itemCats as $itemCat){
          $itemCategories .= "cat-".$itemCat->slug." ";
        }
      }
      $image = wp_get_attachment_image_src( get_post_thumbnail_id( $item->ID ), 'single-post-thumbnail' );
      if($image !== false){
		$image = getRealThumbnailUrl($image[0]);
      }else{
		$image = '';
      }
      if($i < $count)
      {
        global $wpdb;
        $sql = "SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE post_id=".$item->ID." AND meta_key LIKE '%_ait%'";
        $itemMeta = $wpdb->get_results($sql);
        $itemMetaValue = unserialize($itemMeta[0]->meta_value);

        if($itemMetaValue['itemType'] == NULL){$itemMetaValue['itemType']='image';}
        $result .= '<li class="item left tile-'.$i.' '.$itemCategories.' itemType-'.$itemMetaValue['itemType'].'" data-id="id-'.$i.'">';

        $bigImageAlignment = "";
        switch($itemMetaValue['imageAlign']){
          case 'cac':
            $bigImageAlignment = "&amp;a=c";
          break;
          case 'cat':
            $bigImageAlignment = "&amp;a=t";
          break;
          case 'catl':
            $bigImageAlignment = "&amp;a=tl";
          break;
          case 'catr':
            $bigImageAlignment = "&amp;a=tr";
          break;
          case 'cab':
            $bigImageAlignment = "&amp;a=b";
          break;
          case 'cabl':
            $bigImageAlignment = "&amp;a=bl";
          break;
          case 'cabr':
            $bigImageAlignment = "&amp;a=br";
          break;
          case 'cal':
            $bigImageAlignment = "&amp;a=l";
          break;
          case 'car':
            $bigImageAlignment = "&amp;a=r";
          break;
          case 'scale':
            $bigImageAlignment = "&amp;zc=0";
          break;
        }

        if($image){
          switch($itemMetaValue['itemType']){
            case 'image':
              if($display == 'fancybox'){
                $result .= '<div class="tile" style="display: none"><div class="tileWrap" style="overflow: hidden">' . TIMTHUMB_URL . '?src='.$image.'&amp;w=1&amp;h=1'.$bigImageAlignment.'</div><span class="gridgallery-icon"><a class="tileImage" href="'.$image.'">';
              } else {
                $result .= '<div class="tile" style="display: none"><div class="tileWrap" style="overflow: hidden">'.TIMTHUMB_URL . '?src='.$image.'&amp;w=1&amp;h=1'.$bigImageAlignment.'</div><span class="gridgallery-icon"><a class="tileImage" href="'.get_permalink( $item->ID ).'">';
                // style="background: url(\''.TIMTHUMB_URL . '?src='.$image.'&amp;w='.$width.'&amp;h='.$height.$bigImageAlignment.'\') no-repeat center center"
              }
            break;
            case 'website':
                $result .= '<div class="tile" style="display: none"><div class="tileWrap" style="overflow: hidden">'.TIMTHUMB_URL . '?src='.$image.'&amp;w=1&amp;h=1'.$bigImageAlignment.'</div><span class="gridgallery-icon"><a class="tileImage" href="'.$itemMetaValue['websiteLink'].'">';
            break;
            case 'video':
              if($display == 'fancybox'){
                $result .= '<div class="tile" style="display: none"><div class="tileWrap" style="overflow: hidden">'.TIMTHUMB_URL . '?src='.$image.'&amp;w=1&amp;h=1'.$bigImageAlignment.'</div><span class="gridgallery-icon"><a class="tileImage" href="'.$itemMetaValue['videoLink'].'">';
              } else {
                $result .= '<div class="tile" style="display: none"><div class="tileWrap" style="overflow: hidden">'.TIMTHUMB_URL . '?src='.$image.'&amp;w=1&amp;h=1'.$bigImageAlignment.'</div><span class="gridgallery-icon"><a class="tileImage" href="'.get_permalink( $item->ID ).'">';
              }
            break;
          }

          $result .= '<span class="about">';
          $result .= '<span class="about-title">'.$item->post_title.'</span>';
			$itemCats = get_the_terms( $item->ID, 'ait-grid-portfolio-category' );
			$itemCategories = "";
			if($itemCats){
        foreach($itemCats as $itemCat){
  			  $itemCategories .= $itemCat->name." ";
  			}
      }
          $result .= '<span class="about-category">'.$itemCategories.'</span>';
          $result .= '</span>';
          $result .= '</a></span>';
          $result .= '</div>';

          if($description == '1'){
            $descText = $itemMetaValue['itemDescription'];
            $result .= '<div class="tile-desc-wrap"><div class="tile-desc" style="height: '.$descheight.'; margin-right: 0px; overflow: hidden"><h3 class="tile-desc-title">'.$item->post_title.'</h3><p class="tile-desc-cont" style="margin: 0px; padding: 0px;">'.$descText.'</p></div></div>';
          }
        }
        $result .= '</li>';
      }
      $i++;
    }

    $result .= '</ul></div>';
    $result .= '</div>';
    $result .= '</div>';
    /* TILE COUNTING */

  } else {
    $result = '<h2>Insert images into the portfolio</h2>';
  }

  return $result;
}
add_shortcode( "ait-gridgallery", "ait_gridgallery" );