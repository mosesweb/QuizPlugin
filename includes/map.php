<form name="create-quiz-form[]" id="create-quiz-form-<?php echo $_GET['level_number']; ?>" class="create-quiz-form">

<input type="hidden" name="levelname" value="Vocabulary <?php echo $_GET['level_number'] ?>" /><h1>Vocabulary <?php echo $_GET['level_number'] ?> </h1>
        <div class="level-area" data-levelid="<?php echo $_GET['level_number'] ?>">
        
        <table style="">
        <tbody>
        <tr>
        <td style=""></td>
        <td style="">Meaning</td>
        <td style="">Japanese</td>
        <td style="">Hiragana</td>
        <td style="">Katakana</td>
        <td style="">Romaji</td>
        </tr>

        <?php include ("html/word-row.php"); ?>
       
</tbody>
        </table>
</form>
