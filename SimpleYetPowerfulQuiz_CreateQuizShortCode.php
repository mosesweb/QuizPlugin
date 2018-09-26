<?php
include_once('SimpleYetPowerfulQuiz_ShortCodeLoader.php');

class SimpleYetPowerfulQuiz_CreateQuizShortCode extends SimpleYetPowerfulQuiz_ShortCodeLoader {
   /**
    * @param  $atts shortcode inputs
    * @return string shortcode content
    */

   public function handleShortcode($atts) {

    global $wpdb;
    $atts = shortcode_atts( array(
    ), $atts, 'bartag' );   
    
    $simpleplug = new SimpleYetPowerfulQuiz_Plugin();

    $wordtable = $simpleplug->prefixTableName('goiword');
    $cattable = $simpleplug->prefixTableName('goicategories');
    $catgroup = $simpleplug->prefixTableName('goicatgroup');
    $catwordtable = $simpleplug->prefixTableName('goiwordcategories');
    $goiresults = $simpleplug->prefixTableName('goiresults');

    ob_start();
    $userid = get_current_user_id();
    $dirname = dirname(plugin_basename(__FILE__));
    
    $sql = "
    SELECT 
    $catgroup.name AS groupname,
    $catgroup.id AS groupid,
    $catgroup.quiz_desc AS groupdescription,
    $catgroup.slug_name,
    wp_users.display_name AS author,
    $catgroup.category_image AS image

    FROM $catgroup
    
    INNER JOIN wp_users ON wp_users.ID = $catgroup.user_id";

    $result = array();
    $myrows = $wpdb->get_results( $sql, ARRAY_A );
    echo "<div id='quiz-create-container'>";
    include("includes/create.php");
    echo "</div>";
    
    
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
    }
}
?>