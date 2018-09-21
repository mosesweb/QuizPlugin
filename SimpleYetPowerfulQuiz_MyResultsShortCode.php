<?php
include_once('SimpleYetPowerfulQuiz_ShortCodeLoader.php');

class SimpleYetPowerfulQuiz_MyResultsShortCode extends SimpleYetPowerfulQuiz_ShortCodeLoader {
   /**
    * @param  $atts shortcode inputs
    * @return string shortcode content
    */

   public function handleShortcode($atts) {

    global $wpdb;
    $atts = shortcode_atts( array(
        'quiz_category' => 'none',
        'order' => 'asc',
		'baz' => 'default baz'
    ), $atts, 'bartag' );   
    
    $quiz_category = $atts['quiz_category'];
    $simpleplug = new SimpleYetPowerfulQuiz_Plugin();

    $wordtable = $simpleplug->prefixTableName('goiword');
    $cattable = $simpleplug->prefixTableName('goicategories');
    $catwordtable = $simpleplug->prefixTableName('goiwordcategories');
    $goiresults = $simpleplug->prefixTableName('goiresults');

    ob_start();
    $userid = get_current_user_id();
    
    $sql = "
    SELECT 
    $cattable.name AS categoryname,
    $goiresults.procent_correctness,
    $goiresults.result_date
    
    FROM $goiresults
    
    INNER JOIN $cattable
    ON $cattable.id = $goiresults.goicategory_id
    WHERE $goiresults.user_id = '$userid'
    
    ORDER BY $goiresults.Id DESC
    ";

    $myrows = $wpdb->get_results( $sql, OBJECT);
    foreach($myrows as $row)
    {
        if (strtotime($row->result_date) >= strtotime("today"))
        echo "Today " . date_format(new DateTime($row->result_date), "h:i:s A");

        else if (strtotime($row->result_date) >= strtotime("yesterday"))
        echo "Yesterday " . date_format(new DateTime($row->result_date), "h:i:s A");

        else 
        echo date_format( new DateTime($row->result_date), "Y-m-d h:i:s A");

        echo ": " . $row->categoryname . " ";
        echo $row->procent_correctness . "%" . "<br />";
    }
    
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
    }
}
?>