<?php

include_once('SimpleYetPowerfulQuiz_LifeCycle.php');

global $wpdb;

        // $sql = "
        // SELECT 
        // meaning 
        
        // FROM xbrs_simpleyetpowerfulquiz_plugin_goiword
        
        // ";
        // $myrows = $wpdb->get_results( $sql, ARRAY_A);
        // foreach($myrows as $row)
        // {
        //     echo $row['meaning'] . '<br />';
        // }   

?>

<div class="container">  
                <br />  
                <br />  
                <br />  
                <div class="table-responsive">  
                     <h3 align="center">Words</h3><br />  
                     <div id="live_data"></div>                 

                </div>
                <div class="table-responsive">  
                     <h3 align="center">Categories</h3><br />  
                     <div id="live_dataCategories"></div>                 

                </div>

           </div>  
<script>
		jQuery(function($) { // DOM is now ready and jQuery's $ alias sandboxed

            $(document).ready(function(){  
function fetch_data()  
      {  
           $.ajax({  
                url:"/wp-content/plugins/simple-yet-powerful-quiz/ajax/select.php",  
                method:"POST", 
                data:{datatype:'vocablist'}, 
                success:function(data){  
                     $('#live_data').html(data); 
                     console.log("VOCAB!");
                }  
           });  
           // category
           $.ajax({  
                url:"/wp-content/plugins/simple-yet-powerful-quiz/ajax/select.php",  
                method:"POST", 
                data:{datatype:'categorylist'}, 
                success:function(data){  
                     $('#live_dataCategories').html(data); 
                     console.log("CATS!");
                }  
           });  
      } 
      fetch_data();

      $(document).on('click', '#btn_add', function(){  
           var meaning = $(this).parent().parent().find("#meaning").text();  
           var japanese = $(this).parent().parent().find('#japanese').text();
           
           var kanji = $(this).parent().parent().find('#kanji').text();  
           var kana = $(this).parent().parent().find('#kana').text();  
           var romaji = $(this).parent().parent().find('#romaji').text();  

           var categoryselectedid = $(this).parent().parent().find('#categoryfromlistid').val();  
           if(meaning == '')  
           {  
                alert("Enter Meaning");  
                return false;  
           }  
           if(japanese == '')  
           {  
                alert("Enter Japanese");  
                return false;  
           }  
           $.ajax({  
                url:"/wp-content/plugins/simple-yet-powerful-quiz/ajax/insert.php",  
                method:"POST",  
                data:{addtype:'addvocab',meaning:meaning, japanese:japanese, categoryfromlistid: categoryselectedid, kanji: kanji, kana: kana, romaji: romaji},  
                dataType:"text",  
                success:function(data)  
                {  
                     alert(data);  
                     fetch_data();  
                }  
           })  
      });
      // Cat add
      $(document).on('click', '#cat_btn_add', function(){  
           var catname = $('#catname').text();  
           if(catname == '')  
           {  
                alert("Enter Name");  
                return false;  
           }  
           $.ajax({  
                url:"/wp-content/plugins/simple-yet-powerful-quiz/ajax/insert.php",  
                method:"POST",  
                data:{addtype:'addcat', catname:catname},  
                dataType:"text",  
                success:function(data)  
                {  
                     alert(data);  
                     fetch_data();  
                }  
           })  
      });

      function edit_data(type, id, text, column_name)  
      {  
           $.ajax({  
                url:"/wp-content/plugins/simple-yet-powerful-quiz/ajax/edit.php",  
                method:"POST",  
                data:{editype: type, id:id, text:text, column_name:column_name},  
                dataType:"text",  
                success:function(data){  
                  if(data != "" && data != null)
                  {
                        alert(data);
                  }  
                }  
           });  
      }
      $(document).on('blur', '.meaning', function(){ 
           var editype = $(this).closest('.table-responsive').attr('data-list');
           var id = $(this).data("id1");  
           var meaning = $(this).text();  
           edit_data(editype, id, meaning, "meaning");  
      });
      $(document).on('blur', '.name', function(){ 
           var editype = $(this).closest('.table-responsive').attr('data-list');
           var id = $(this).data("id1");  
           var catname = $(this).text();  
           edit_data(editype, id, catname, "name");  
      });
      $(document).on('change', '.categoryfromlistid', function(){
            console.log("change..");
           var editype = $(this).closest('.table-responsive').attr('data-list');
           var id = $(this).data("id4");  
           var catid = $(this).val();  
           edit_data(editype, id, catid, "categoryfromlistid");
      });
      $(document).on('blur', '.japanese', function(){ 
            var editype = $(this).closest('.table-responsive').attr('data-list');
 
           var id = $(this).data("id2");  
           var japanese = $(this).text();  
           edit_data(editype, id,japanese, "japanese");  
      });
      $(document).on('blur', '.kanji', function(){ 
            var editype = $(this).closest('.table-responsive').attr('data-list');
 
           var id = $(this).data("id2");  
           var kanji = $(this).text();  
           edit_data(editype, id,kanji, "kanji");  
      });
      $(document).on('blur', '.kana', function(){ 
            var editype = $(this).closest('.table-responsive').attr('data-list');
 
           var id = $(this).data("id2");  
           var kana = $(this).text();  
           edit_data(editype, id,kana, "kana");  
      });
      $(document).on('blur', '.romaji', function(){ 
            var editype = $(this).closest('.table-responsive').attr('data-list');
 
           var id = $(this).data("id2");  
           var romaji = $(this).text();  
           edit_data(editype, id,romaji, "romaji");  
      });
      $(document).on('click', '.btn_delete', function(){ 
            var deltype = $(this).closest('.table-responsive').attr('data-list');


           var id=$(this).data("id3");  
           if(confirm("Are you sure you want to delete this?"))  
           {  
                $.ajax({  
                     url:"/wp-content/plugins/simple-yet-powerful-quiz/ajax/delete.php",  
                     method:"POST",  
                     data:{deltype:deltype, id:id},  
                     dataType:"text",  
                     success:function(data){  
                          alert(data);  
                          fetch_data();  
                     }  
                });  
           }  
      });  
    });
    });
</script>