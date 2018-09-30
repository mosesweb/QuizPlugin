<?php 
include( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
include_once('../SimpleYetPowerfulQuiz_Plugin.php');
global $wpdb;

        if(isset($_POST['data']))
        {
            for($i = 0; $i < count($_POST['data']);$i++)
            {
                //print_r($_POST['data'][$i]);

                parse_str($_POST['data'][$i], $get_array);
                $str .= $get_array["levelname"];
                $str .= count($get_array["meaning"]);

                // Loop through all data
                for($s = 0; $s < count($get_array["meaning"]);$s++)
                {
                    $meaning = "";
                    $japanese = "";
                    $hiragana = "";
                    $katakana = "";
                    $romaji = "";

                    if(isset($get_array["meaning"][$s]))
                    $meaning = $get_array["meaning"][$s];
                    
                    if(isset($get_array["japanese"][$s]))
                    $japanese = $get_array["japanese"][$s];
                    
                    if(isset($get_array["hiragana"][$s]))
                    $hiragana = $get_array["hiragana"][$s];
                    
                    if(isset($get_array["katakana"][$s]))
                    $katakana = $get_array["katakana"][$s];

                    if(isset($get_array["romaji"][$s]))
                    $romaji = $get_array["romaji"][$s];

                    // fix insert logic..
                    echo "word:" . $meaning . $japanese . $hiragana . $katakana . $romaji;
                }   
            }
        }
        else
        echo var_dump($_POST); 
?>