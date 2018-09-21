<?php  
header('Content-Type: text/html; charset=utf-8');
include( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
include_once('../SimpleYetPowerfulQuiz_Plugin.php');

class SimpleYetPowerfulQuiz_Select extends SimpleYetPowerfulQuiz_Plugin {
    
    function getData()
    {
        $simpleplug = new SimpleYetPowerfulQuiz_Plugin();
        global $wpdb;
         $output = '';
         $wordtable = $simpleplug->prefixTableName('goiword');
         $cattable = $simpleplug->prefixTableName('goicategories');
         $catwordtable = $simpleplug->prefixTableName('goiwordcategories');
         $catgroup = $simpleplug->prefixTableName('goicatgroup');

         $sqladvanced = "
         SELECT 
         $wordtable.japanese, 
         $wordtable.meaning, 
         $wordtable.kanji, 
         $wordtable.kana, 
         $wordtable.romaji, 
         $wordtable.japanese, 
         $wordtable.image, 
         $wordtable.image_author, 
         $wordtable.imgauthor_link, 
         $wordtable.id as id, 
         $cattable.name AS categoryname, 
         $cattable.id AS categoryid, 
         $cattable.slug_name 
         
         FROM $wordtable
         INNER JOIN $catwordtable 
         ON $wordtable.id = $catwordtable.word_id
         INNER JOIN $cattable
         ON $cattable.id = $catwordtable.category_id
         ";
         $catsql = "SELECT * FROM $cattable";
         $cats = $wpdb->get_results( $catsql, ARRAY_A);
         $sql = "SELECT id, meaning, japanese, kanji FROM `$wordtable` ORDER BY id DESC";  

         switch($_POST['datatype'])
         {
        case 'vocablist':
         $result = $wpdb->get_results( $sqladvanced, ARRAY_A);

         $data = array();
    
         foreach($result as $row)  
        {
            $itemName = $row["categoryname"];
            if (!array_key_exists($itemName, $data)) {
                $data[$itemName] = array();
        }
        $data[$itemName][] = $row;
        }
        foreach ($data as $itemName => $rows) {
            $output .= '  
            <div class="table-responsive" data-list="vocablist"><h2>' . $itemName . '</h2><br />  
                 <table class="table table-bordered">  
                      <tr>  
                           <th width="10%">Id</th>  
                           <th width="20%">meaning</th>  
                           <th width="10%">japanese</th>
                           <th width="10%">kanji</th> 
                           <th width="10%">kana</th> 
                           <th width="10%">romaji</th>   
                           <th width="10%">category</th> 
                           <th width="20%">Delete</th>  
                      </tr>
                      
                      <tr>  
                      <td></td>  
                      <td id="meaning" data-id1="'.$row["id"].'" contenteditable></td>  
                      <td id="japanese" data-id2="'.$row["id"].'" contenteditable></td>  
                      <td id="kanji" data-id2="'.$row["id"].'" contenteditable></td> 
                      <td id="kana" data-id2="'.$row["id"].'" contenteditable></td> 
                      <td id="romaji" data-id2="'.$row["id"].'" contenteditable></td> 

                        <td class="category" data-id3="'.$row["id"].'">
                        <select id="categoryfromlistid" data-id4="'.$row["id"].'" name="categoryfromlistid">
                        ';
                        foreach($cats as $cat)
                        {
                            $selected = "";
                            if($itemName == $cat['name'])
                            {
                                $selected = "selected";
                            }
                            $output .= '
                            <option '.$selected.' value="'.$cat['id'].'">' . $cat['name'] .'</option>
                            ';
                        }
                        $output .=
                        '
                        </select>
                      
                      </td>  
                      <td><button type="button" name="btn_add" id="btn_add" class="btn btn-xs btn-success">+</button></td>
                    </tr>  
                      '; 

            foreach ($rows as $row) {

                $output .= '
                  
                <tr>  
                     <td>'.$row["id"].'</td>  
                     <td class="meaning" data-id1="'.$row["id"].'" contenteditable>'.$row["meaning"].'</td>  
                     <td class="japanese" data-id2="'.$row["id"].'" contenteditable>'.$row["japanese"].'</td>  
                     <td class="kanji" data-id2="'.$row["id"].'" contenteditable>'.$row["kanji"].'</td>  
                     <td class="kana" data-id2="'.$row["id"].'" contenteditable>'.$row["kana"].'</td>  
                     <td class="romaji" data-id2="'.$row["id"].'" contenteditable>'.$row["romaji"].'</td>  
                     <td class="category" data-id3="'.$row["categoryid"].'"> 
                     <select class="categoryfromlistid" data-id4="'.$row["id"].'" name="categoryfromlistid">';
                     foreach($cats as $cat)
                     {
                         $selected = "";
                         if($row['categoryid'] == $cat['id'])
                         {
                             $selected = "selected";
                         }
                         $output .= '
                         <option '.$selected.' value="'.$cat['id'].'">' . $cat['name'] .'</option>
                         ';
                     }
                      $output .= 
                     '</select></td>  
                     <td><button type="button" name="delete_btn" data-id3="'.$row["id"].'" class="btn btn-xs btn-danger btn_delete">x</button></td>  
                </tr>  
           '; 
            }
            $output .= '</table></div>';
          }
              
         echo $output;  

         break;
         
         case 'categorylist':
         $sql = "SELECT * FROM `$cattable` ORDER BY id DESC";  
         $result = $wpdb->get_results( $sql, ARRAY_A);

         $output .= '  
              <div class="table-responsive" data-list="catlist">  
                   <table class="table table-bordered">  
                        <tr>  
                             <th width="5%">Id</th> 
                             <th width="5%">Quiz group id</th>
                             <th width="10%">Slug name</th> 
                             <th width="30%">Name</th>  
                             <th width="40%">Image</th>  
                             <th width="10%">Delete</th>  
                        </tr>';  
         if($wpdb->num_rows > 0)  
         {  
            foreach($result as $row)
            
              {  
                   $output .= '  
                        <tr>  
                            <td>'.$row["id"].'</td> 
                            <td>'.$row["group_id"].'</td> 
                            <td>'.$row["slug_name"].'</td>   
                            <td class="name" data-id1="'.$row["id"].'" contenteditable>'.$row["name"].'</td>  
                            <td class="Image" data-id2="'.$row["id"].'" contenteditable>'.$row["Image"].'</td>  
                            <td><button type="button" name="delete_btn" data-id3="'.$row["id"].'" class="btn btn-xs btn-danger btn_delete">x</button></td>  
                        </tr>  
                   ';  
              }  
              $output .= '  
                   <tr>  
                        <td></td>     
                        <td id="group_id" contenteditable></td>    
                        <td></td>  
                        <td id="catname" contenteditable></td>  
                        <td id="Image" contenteditable></td>  
                        <td><button type="button" name="cat_btn_add" id="cat_btn_add" class="btn btn-xs btn-success">+</button></td>  
                   </tr>  
              ';  
         }  
         else  
         {  
              $output .= '<tr>  
                                  <td colspan="4">Data not Found</td>  
                             </tr>';  
         }  
         $output .= '</table>  
              </div>';  
         echo $output;

    break;

    case 'groupcategorylist':
         $sql = "SELECT * FROM `$catgroup` ORDER BY id DESC";  
         $result = $wpdb->get_results( $sql, ARRAY_A);

         $output .= '  
              <div class="table-responsive" data-list="groupcategorylist">  
                   <table class="table table-bordered">  
                        <tr>  
                             <th width="10%">Id</th> 
                             <th width="10%">Slug name</th> 
                             <th width="30%">Name</th>  
                             <th width="40%">Image</th>  
                             <th width="10%">Delete</th>  
                        </tr>';  
         if($wpdb->num_rows > 0)  
         {  
            foreach($result as $row)
            
              {  
                   $output .= '  
                        <tr>  
                             <td>'.$row["id"].'</td> 
                             <td>'.$row["slug_name"].'</td>   
                             <td class="name" data-id1="'.$row["id"].'" contenteditable>'.$row["name"].'</td>  
                             <td class="Image" data-id2="'.$row["id"].'" contenteditable>'.$row["Image"].'</td>  
                             <td><button type="button" name="delete_btn" data-id3="'.$row["id"].'" class="btn btn-xs btn-danger btn_delete">x</button></td>  
                        </tr>  
                   ';  
              }  
              $output .= '  
                   <tr>  
                        <td></td>  
                        <td></td>  
                        <td id="groupcatname" contenteditable></td>  
                        <td id="Image" contenteditable></td>  
                        <td><button type="button" name="group_cat_btn_add" id="group_cat_btn_add" class="btn btn-xs btn-success">+</button></td>  
                   </tr>  
              ';  
         }  
         else  
         {  
              $output .= '<tr>  
                                  <td colspan="4">Data not Found</td>  
                             </tr>';  
         }  
         $output .= '</table>  
              </div>';  
         echo $output;

    }
}
}

$SimpleYetPowerfulQuiz_SelectObj = new SimpleYetPowerfulQuiz_Select();
$SimpleYetPowerfulQuiz_SelectObj->getData();
 ?>