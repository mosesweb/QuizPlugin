<div class="map-area">
    <h1>New quiz</h1>
    <form>
        <label>Name</label>
        <input name="mapname" type="text" /> <br />
     
        <h1>Vocabulary</h1>
        <div class="level-area">
        
        <table style="">
        <tbody>
        <tr>
        <td style="">Meaning</td>
        <td style="">Japanese</td>
        <td style="">Hiragana</td>
        <td style="">Katakana</td>
        <td style="">Romaji</td>
        </tr>
        <?php include ("html/word-row.html"); ?>
        </tbody>
        </table>
        <script>
         var el = "";
         var allGood=true;
        jQuery(".word-row input").live('change', function()
        {
           el = jQuery(this).parent().parent().parent().find(".word-row").last();
            allGood=true;
                var lastInputField=0;
                elements = jQuery(el).find("input.meaning, input.japanese, input.hiragana, input.katakana, input.romaji");
                jQuery(elements).each(function() {
                    if (jQuery(this).val() =="") {
                        allGood=false;
                        return false;
                    }
                    lastInputField++;
                });
                if(allGood)
                {                
                    jQuery.get("/wp-content/plugins/<?php echo $dirname ?>/includes/html/word-row.html", function(data){
                    jQuery("tbody").before().append(data);
                    allGood = false;
                });
            }

        })
        </script>
        <!-- DivTable.com -->
        
        
        
        
        
        <div class="word">
            
          
        </div>
        </div>
    </form>
</div>