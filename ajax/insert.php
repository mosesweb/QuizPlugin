<?php  
include( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
include_once('../SimpleYetPowerfulQuiz_Plugin.php');

class SimpleYetPowerfulQuiz_Insert extends SimpleYetPowerfulQuiz_Plugin {
    
    public function aslugify($text)
    {
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
    }

    function insertData($meaning, $japanese, $kanji, $kana, $romaji, $categoryid)
    {
        $plug = new SimpleYetPowerfulQuiz_Plugin();
        global $wpdb;
        
        $wordtable = $plug->prefixTableName('goiword');
        $cattable = $plug->prefixTableName('goicategories');
        $catwordtable = $plug->prefixTableName('goiwordcategories');

        $sql = "INSERT INTO $wordtable(meaning, japanese, kanji, kana, romaji) VALUES('".$meaning."', '".$japanese."', '".$kanji."', '".$kana."', '".$romaji."')";  
        if($wpdb->query($sql))  
        {  
            $lastid = $wpdb->insert_id;
            echo 'Data Inserted' . $lastid;  

            $sqlCatWord = "INSERT INTO $catwordtable(word_id, category_id) VALUES('".$lastid."', '". $categoryid."')";  
            if($wpdb->query($sqlCatWord))  
            {  
                echo 'And Cat Data Inserted';  
            }
        }
    }
    function insertCatData($catname)
    {
        $plug = new SimpleYetPowerfulQuiz_Plugin();
        global $wpdb;
        
        $wordtable = $plug->prefixTableName('goiword');
        $cattable = $plug->prefixTableName('goicategories');
        $catwordtable = $plug->prefixTableName('goiwordcategories');
        $timestamp = time();
        $slugname = $this->aslugify( $catname . '_' .  $timestamp );
        $sql = "INSERT INTO $cattable(name, slug_name) VALUES('".$catname."', '".$slugname."')";  
        if($wpdb->query($sql))  
        {  
                echo 'Data Inserted';  
        }
    }
    function insertGroupCatData($gcatname)
    {
        $plug = new SimpleYetPowerfulQuiz_Plugin();
        global $wpdb;
        
        $wordtable = $plug->prefixTableName('goiword');
        $cattable = $plug->prefixTableName('goicategories');
        $catgroup = $plug->prefixTableName('goicatgroup');
        $catwordtable = $plug->prefixTableName('goiwordcategories');
        $timestamp = time();
        $slugname = $this->aslugify( $gcatname . '_' .  $timestamp );
        $sql = "INSERT INTO $catgroup(name, slug_name) VALUES('".$gcatname."', '".$slugname."')";  
        if($wpdb->query($sql))  
        {  
            echo 'Data Inserted';  
        }
    }
}
$insert = new SimpleYetPowerfulQuiz_Insert();
if($_POST["addtype"] == 'addvocab')
{
    $insert->insertData($_POST["meaning"], $_POST["japanese"], $_POST["kanji"], $_POST["kana"], $_POST["romaji"], $_POST["categoryfromlistid"]);

}
if($_POST["addtype"] == 'addcat')
{
    $insert->insertCatData( $_POST["catname"]);
}
if($_POST["addtype"] == 'addgroupcat')
{
    $insert->insertGroupCatData( $_POST["groupcatname"]);
}
 ?>