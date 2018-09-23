<?php  
include( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
include_once('../SimpleYetPowerfulQuiz_Plugin.php');

class SimpleYetPowerfulQuiz_Edit extends SimpleYetPowerfulQuiz_Plugin {
    
    function editDataVocab($iid, $itext, $icol)
    {
        global $wpdb;
        $plug = new SimpleYetPowerfulQuiz_Plugin();

        $wordtable = $plug->prefixTableName('goiword');

        $id = $iid;  
        $text = $itext;  
        $column_name = $icol;  
        $sql = "UPDATE $wordtable SET ".$column_name."='".$text."' WHERE id='".$id."'";  
        if($wpdb->query($sql))  
        {  
            echo 'Data Updated';  
        }  
    }
    function editDataCat($iid, $itext, $icol)
    {
        global $wpdb;
        $plug = new SimpleYetPowerfulQuiz_Plugin();

        $cattable = $plug->prefixTableName('goicategories');
        $catwordtable = $plug->prefixTableName('goiwordcategories');

        $id = $iid;  
        $text = $itext;  
        $column_name = $icol;  
        if($column_name == 'categoryfromlistid')
        {
            $sql = "UPDATE $catwordtable SET category_id = '".$text."' WHERE word_id = '".$id."'";  
            if($wpdb->query($sql))  
            {  
                echo 'Category Changed';  
            }
        }
        else
        {
            $sql = "UPDATE $cattable SET ".$column_name."='".$text."' WHERE id='".$id."'";  
            if($wpdb->query($sql))  
            {  
                echo 'Data Updated';  
            }
        }  
  
    }
    function editDataGroupCat($iid, $itext, $icol)
    {
        global $wpdb;
        $plug = new SimpleYetPowerfulQuiz_Plugin();

        $cattable = $plug->prefixTableName('goicatgroup');

        $id = $iid;  
        $text = $itext;  
        $column_name = $icol;  

        $sql = "UPDATE $cattable SET ".$column_name."='".$text."' WHERE id='".$id."'";  
        if($wpdb->query($sql))  
        {  
            echo 'Data Updated';  
        }
  
    }
}
$edit = new SimpleYetPowerfulQuiz_Edit();
if($_POST['editype'] == 'vocablist')
{
    $edit->editDataVocab($_POST["id"], $_POST["text"], $_POST["column_name"]);
}
if($_POST['editype'] == 'catlist')
{
    $edit->editDataCat($_POST["id"], $_POST["text"], $_POST["column_name"]);
}
if($_POST['editype'] == 'groupcategorylist')
{
    $edit->editDataGroupCat($_POST["id"], $_POST["text"], $_POST["column_name"]);
}
 ?>