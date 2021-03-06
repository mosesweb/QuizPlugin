<?php
include_once('SimpleYetPowerfulQuiz_ShortCodeLoader.php');

class SimpleYetPowerfulQuiz_QuizShortCode extends SimpleYetPowerfulQuiz_ShortCodeLoader {
   /**
    * @param  $atts shortcode inputs
    * @return string shortcode content
    */

    public function randWithout($from, $to, array $exceptions) {
        sort($exceptions); // lets us use break; in the foreach reliably
        $number = rand($from, $to - count($exceptions)); // or mt_rand()
        foreach ($exceptions as $exception) {
            if ($number >= $exception) {
                $number++; // make up for the gap
            } else /*if ($number < $exception)*/ {
                break;
            }
        }
        return $number;
    }

    function getRandomNum()
    {
        $randomnum = rand(0, $array_max);
    }
    
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
    $catwordtable = $simpleplug->prefixTableName('goiwordcategories');


    ob_start();
    ?>
    <div class="wordpage"><!-- Version 2 -->
    <?php 
    $wpdb->set_charset($wpdb->dbh,"utf8");
    $vocabmode = $atts['order'];
    $current_catname = "";
    $quiz_extra = "";
    $moretext = "<h1 class=\"quizheadline\">$current_catname Vocabulary Quiz</h1> | <a href=\"/words/$quiz_category\" target=\"_blank\">Word List</a>";
    if($quiz_extra != NULL)
    {
        $moretext .= "<div class=\"quiz-extra\">" . $quiz_extra . "</div>";
    }
    echo "<div class=\"quizheader\"><div class=\"vocabname\">$quiz_category</div>
    <div class=\"vocabmode\">$vocabmode</div>
    $moretext
    </div>"; ?>
    <?php
    // https://codex.wordpress.org/Rewrite_API/add_rewrite_rule

    $sql = "
    SELECT word.japanese, word.meaning, word.kanji, word.romaji, word.japanese, word.image, word.image_author, word.imgauthor_link, word.id as WID, 
    categories.name, categories.slug_name 
    FROM word
    INNER JOIN wordcategories 
    ON word.id = wordcategories.word_id
    INNER JOIN categories
    ON categories.id = wordcategories.category_id
    WHERE categories.slug_name = '$quiz_category'";

    $sqlv2 = "
    SELECT 
    $wordtable.japanese, 
    $wordtable.meaning, 
    $wordtable.kanji, 
    $wordtable.romaji, 
    $wordtable.japanese, 
    $wordtable.image, 
    $wordtable.image_author, 
    $wordtable.imgauthor_link, 
    $wordtable.id as WID, 
    $cattable.name, 
    $cattable.slug_name 
    
    FROM $wordtable
    
    INNER JOIN $catwordtable 
    ON $wordtable.id = $catwordtable.word_id
    
    INNER JOIN $cattable
    ON $cattable.id = $catwordtable.category_id
    WHERE $cattable.slug_name = '$quiz_category'";

$myrows = $wpdb->get_results( $sqlv2, ARRAY_A);

$array_max = $wpdb->num_rows - 1; // so we dont check for non existent which is over last element (0 takes up first row)
$amountofquestions = $wpdb->num_rows; // amount of questions
$number = 0;

if($wpdb->num_rows > 0)
{
// Works perfectly. if you want you can shuffle all words..
if($atts['order'] == "random")
{
    shuffle($myrows);
}
if($atts['order'] == "desc")
{
    $myrows = array_reverse($myrows);
}
//shuffle($myrows);
/* fetch associative array */
$post = array(); 

//while ($row = $rresult->fetch_assoc())
echo '<div class="main_questionsarea">';
foreach($myrows as $row)
{
    $number++;
    $numbers = range(0, $array_max);
    shuffle($numbers);

    echo "<div class=\"answer-options box-$number\">";
    echo "<div class=\"question\">";
    echo"
    <div class=\"questionnumber\">Question <span class=\"current-q\">$number</span> / <span class=\"total-q\">$amountofquestions</span>. </div>
    <div class=\"questiontext\">What is <span class=\"questionword\">" . $row['meaning'] . "</span> in Japanese?</div>
    <div class=\"clear\"></div></div>";

    $q1 = $this->randWithout(0, $array_max, array());
    $q2 = $this->randWithout(0, $array_max, array($q1));
    $q3 = $this->randWithout(0, $array_max, array($q1, $q2));
    
    while($myrows[$q1]['japanese'] == $row['japanese'] || $myrows[$q1]['japanese'] == $myrows[$q2]['japanese'] || $myrows[$q1]['japanese'] == $myrows[$q3]['japanese'])
    {
        //echo "sql1 same.. gogogog";
        $q1 = $this->randWithout(0, $array_max, array());
    }
    while($myrows[$q2]['japanese'] == $row['japanese'] || $myrows[$q2]['japanese'] == $myrows[$q3]['japanese'] || $myrows[$q2]['japanese'] == $myrows[$q1]['japanese'])
    {
        //echo "sq2 same.. gogogog";
        $q2 = $this->randWithout(0, $array_max, array($q1));
    }
    while($myrows[$q3]['japanese'] == $row['japanese'] || $myrows[$q3]['japanese'] == $myrows[$q1]['japanese'] || $myrows[$q3]['japanese'] == $myrows[$q2]['japanese'])
    {
        //echo "sq3 same.. gogogog";
        $q3 = $this->randWithout(0, $array_max, array($q2, $q3));
    }
    
    $answeroptionsa = array(
    0 => $myrows[$q1]['japanese'],
    1 => $myrows[$q2]['japanese'],
    2 => $myrows[$q3]['japanese'],
    3 => $row['japanese']);
    
    // Shuffle order! (otherwise the correct answer would be the [3] last one)
    shuffle($answeroptionsa);
    
    echo "<div class=\"answer unselected\">". $answeroptionsa[0] . "</div>";
    echo "<div class=\"answer unselected\">". $answeroptionsa[1] . "</div>";
    echo "<div class=\"answer unselected\">". $answeroptionsa[2] . "</div>";
    echo "<div class=\"answer unselected\">". $answeroptionsa[3] . "</div>";
    echo "<div class=\"clearall\"></div>";
    echo "</div>";
}
echo "</div>";
?>
<div class="result" style="display:none">
<h1>Completion</h1>
<?php 
$my_plugin = plugin_dir_url('simple-yet-powerful-quiz') . '/simple-yet-powerful-quiz';

    echo '<img src="'.$my_plugin.'/images/repeat.png" class="repeat-afterquiz" />';

?>
<div class="result-left">
    <div class="result-header">You got 4 correct answers out of 22</div>
    <div class="result-text">Correct procentage: 18%</div>
</div>

<div class="clear"></div>
</div>
<div class="score">
<div class="result-info">
    <div class="result-info-text"></div>
    <!-- <div class="wrong-info">Hm</div>
    <div class="correct-info">Hm</div> -->
    <div class="result-info-word-info"></div>
</div>
<div class="correct-box">
<p>Correct</p>
<div class="correct">0</div>
</div>
<div class="wrong-box">
<p>Wrong</p>
<div class="wrong">0</div>
</div>
</div>
</div>
<?php } else echo "Sorry no category found"; 
    
$output = ob_get_contents();
ob_end_clean();
return $output;

}
}
?>