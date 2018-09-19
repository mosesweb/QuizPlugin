<?php  
include( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
include_once('../SimpleYetPowerfulQuiz_Plugin.php');

class SimpleYetPowerfulQuiz_Delete extends SimpleYetPowerfulQuiz_Plugin {

    function DeleteWordData($iid)
    {
        $plug = new SimpleYetPowerfulQuiz_Plugin();
        global $wpdb;
        $wordtable = $plug->prefixTableName('goiword');
        
        $sql = "DELETE FROM $wordtable WHERE id = '".$iid."'";  
        if($wpdb->query($sql))  
        {  
            echo 'Word Data Deleted';  
        }  
    }
    function DeleteCatData($iid)
    {
        $plug = new SimpleYetPowerfulQuiz_Plugin();
        global $wpdb;
        $cattable = $plug->prefixTableName('goicategories');
        
        $sql = "DELETE FROM $cattable WHERE id = '".$iid."'";  
        if($wpdb->query($sql))  
        {  
            echo 'Cat Data Deleted';  
        }  
    }
}

$del = new SimpleYetPowerfulQuiz_Delete();
if($_POST['deltype'] = 'catlist')
{
    $del->DeleteCatData($_POST["id"]);

}
if($_POST['deltype'] = 'vocablist')
{
    $del->DeleteWordData($_POST["id"]);
}
?>