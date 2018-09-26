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
    $catgroup = $simpleplug->prefixTableName('goicatgroup');
    $catwordtable = $simpleplug->prefixTableName('goiwordcategories');
    $goiresults = $simpleplug->prefixTableName('goiresults');

    ob_start();
    $userid = get_current_user_id();
    
    $sql = "
    SELECT 
    $cattable.name AS categoryname,
    $goiresults.procent_correctness,
    $goiresults.result_date,
    $catgroup.name AS groupname
    
    FROM $goiresults
    
    INNER JOIN $cattable
    ON $cattable.id = $goiresults.goicategory_id

    INNER JOIN $catgroup
    ON $catgroup.id = $cattable.group_id

    WHERE $goiresults.user_id = '$userid'
    ORDER BY $goiresults.result_date DESC
    ";

    $result = array();
    $myrows = $wpdb->get_results( $sql, ARRAY_A );
    foreach($myrows as $row)
    {
        $result[$row['groupname']][] = $row;

        
    }
    function arraySort($input,$sortkey){
        foreach ($input as $key=>$val) $output[$val[$sortkey]][]=$val;
        return $output;
      }
      $myArray = arraySort($myrows,'groupname');
    foreach($myArray as $row => $value)
    {
        echo "<b>" . $row . "</b><br />";
        foreach( $value as $key=>$inner_row)
        {
            if($key > 2)
            break;

            echo "<div style='
            padding: 10px;
            margin-top:5px;
            margin-bottom:5px;
            background:#eaeaea;
            '>";
            if (strtotime($inner_row["result_date"]) >= strtotime("today"))
            echo "Today " . date_format(new DateTime($inner_row["result_date"]), "h:i:s A");
            else if (strtotime($inner_row["result_date"]) >= strtotime("yesterday"))
            echo "Yesterday " . date_format(new DateTime($inner_row["result_date"]), "h:i:s A");
            else 
            echo date_format( new DateTime($inner_row["result_date"]), "Y-m-d h:i:s A");
            echo "<br /><b> " . $inner_row["categoryname"] . "</b>: ";
            echo $inner_row["procent_correctness"] . "%";   
            echo "<div style='
            height: 5px;
            '>";      
            echo "</div></div>";
        }
       
    }
    
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
    }
}
?>