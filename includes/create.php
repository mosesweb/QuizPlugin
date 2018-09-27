<div class="map-area">
    <h1>New quiz</h1>
        <label>Name</label>
        <input name="mapname" type="text" /> <br />
     
        <?php include("map.php"); ?>
        <script>
         var el = "";
        jQuery(".word-row input").live('change', function()
        {
            jQuery(this).closest('.level-area').find(".word-row").each(function(index)
            {
                jQuery(this).find("td.order").text(index+1);
            });
           el = jQuery(this).closest(".word-row").last();
            var allGood=true;
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
                    jQuery.get("/wp-content/plugins/<?php echo $dirname ?>/includes/html/word-row.php", function(data){
                    jQuery(el).closest('tbody').first().before().append(data);
                    allGood = false;
                });
            }
            jQuery('.level-area').each(function() 
            {
                if(jQuery(this).find('.word-row').length < 3)
                {
                    jQuery(".add-new-level").hide();
                }
            });
            if(jQuery(this).closest('.level-area').find(".word-row").length > 3)
            {
                jQuery(".add-new-level").show();
            }
        });
        jQuery(".add-new-level").live('click', function()
        {
            jQuery.get("/wp-content/plugins/<?php echo $dirname ?>/includes/map.php", 
            {
                level_number: jQuery('.level-area').length+1
            }, function(data) {
               jQuery('.level-area').after(data);
            });
            
        });
        </script>
        <!-- DivTable.com -->
        
        </div>
        <div class="add-new-level">Add level</div>
        <div id="create-quiz">Create quiz</div>

    <script>
    jQuery('#create-quiz').live('click', function()
    {
        var forms = [];
        var thing = [];
        //forms = jQuery('.create-quiz-form').serialize();

        jQuery('.create-quiz-form').each(function()
        {
            forms.push(jQuery(this).serialize());
        });
        jQuery.post("/wp-content/plugins/<?php echo $dirname ?>/includes/create_quiz_post.php", 
        {
            data: forms,
        }, 
        function(result){
            alert(result);
            console.log(result);
        });
    });
    </script>
</div>