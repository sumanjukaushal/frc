<html>
    <head>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
         <script>
            $(function() {
                $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
            });
            $(function() {
                $( "#datepicker1" ).datepicker({ dateFormat: 'yy-mm-dd' });
            });
         </script>
        <?php 
            require_once("../../wp-config.php");
            require_once('../../wp-load.php');
            global $wpdb;
            defined('DS') or define('DS', DIRECTORY_SEPARATOR);
            require_once(WP_CONTENT_DIR.DS.'frc_includes/header.php');
            new WP_Query( array(
                's' => 'Boyup Brook',
            'ep_integrate'   => false,
            'post_type'      => 'post',
            'posts_per_page' => 20,
        ) );
            if(isset($_REQUEST['submit'])){
                $postTitle = $_REQUEST['search_item_title'];
                $catTax = array('taxonomy' => 'ait-items','field' => 'term_id','terms' => array(5, 191), 'include_children' => false);
                $taxQry = array($catTax);
                $args['tax_query'] = $taxQry;
                $args = array( 
                            'ep_integrate' => true, //elastic search
                            'post_type' => 'ait-item',
                            //'post__in'=>array($postId),
                            'title'=>$postTitle,
                            );
    
                $the_query = new WP_Query( $args );
                if ( $the_query->have_posts() ) {
                    echo'<pre>';print_r($the_query->posts);
                }else{
                    echo'bye';
                }
            }
        ?>
    </head>
    <body>
        <form action='wp-query.php' method='get'>
            <fieldset style='width:78%'>
                <legend>Search Items</legend>
                <input type='text' name='search_item_title' value = '' placeholder='ITEM TITLE' style='width:200px;'/>
                <span style='padding-left:20px;'>
                <input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/>
                </span>
            </fieldset>
        </form>        
    </body>
</html>