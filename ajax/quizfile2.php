	
		<div class="wordpage"><!-- Version 2 -->
		<?php 
		$mysqli = new mysqli("", "", "", "");
		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}
		mysqli_set_charset($mysqli,"utf8");
		
		$moretext = "<h1 class=\"quizheadline\">$current_catname Vocabulary Quiz</h1> | <a href=\"/words/$whatcategory\" target=\"_blank\">Word List</a>";
		if($quiz_extra != NULL)
		{
			$moretext .= "<div class=\"quiz-extra\">" . $quiz_extra . "</div>";
		}
		echo "<div class=\"quizheader\">$moretext
		</div>"; ?>
		<?php
		// https://codex.wordpress.org/Rewrite_API/add_rewrite_rule
		$category_variable = $whatcategory;
		if(!isset($category_variable) || empty($category_variable))
		{
			$category_variable = "sea_animals";
		}
		$sql = "
		SELECT word.japanese, word.meaning, word.kanji, word.romaji, word.japanese, word.image, word.image_author, word.imgauthor_link, word.id as WID, 
		categories.name, categories.slug_name 
		FROM word
		INNER JOIN wordcategories 
		ON word.id = wordcategories.word_id
		INNER JOIN categories
		ON categories.id = wordcategories.category_id
		WHERE categories.slug_name = '$category_variable'";

//$myrows = $wpdb->get_results( $sql, ARRAY_A);
$rresult = $mysqli->query($sql);

$myrows = $rresult->fetch_all(MYSQLI_ASSOC);

$array_max = $rresult->num_rows - 1; // so we dont check for non existent which is over last element (0 takes up first row)
$amountofquestions = $rresult->num_rows; // amount of questions
$number = 0;
function randWithout($from, $to, array $exceptions) {
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
if($rresult->num_rows > 0)
{
	// Works perfectly. if you want you can shuffle all words..
	//shuffle($myrows);
	/* fetch associative array */
	$post = array(); 

	//while ($row = $rresult->fetch_assoc())
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

		$q1 = randWithout(0, $array_max, array());
		$q2 = randWithout(0, $array_max, array($q1));
		$q3 = randWithout(0, $array_max, array($q1, $q2));
		
		while($myrows[$q1]['japanese'] == $row['japanese'] || $myrows[$q1]['japanese'] == $myrows[$q2]['japanese'] || $myrows[$q1]['japanese'] == $myrows[$q3]['japanese'])
		{
			//echo "sql1 same.. gogogog";
			$q1 = randWithout(0, $array_max, array());
		}
		while($myrows[$q2]['japanese'] == $row['japanese'] || $myrows[$q2]['japanese'] == $myrows[$q3]['japanese'] || $myrows[$q2]['japanese'] == $myrows[$q1]['japanese'])
		{
			//echo "sq2 same.. gogogog";
			$q2 = randWithout(0, $array_max, array($q1));
		}
		while($myrows[$q3]['japanese'] == $row['japanese'] || $myrows[$q3]['japanese'] == $myrows[$q1]['japanese'] || $myrows[$q3]['japanese'] == $myrows[$q2]['japanese'])
		{
			//echo "sq3 same.. gogogog";
			$q3 = randWithout(0, $array_max, array($q2, $q3));
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

?>
<div class="result" style="display:none">
<h1>Completion</h1>
	<div class="result-left">
		<div class="result-header">You got 4 correct answers out of 22</div>
		<div class="result-text">Correct procentage: 18%</div>
	</div>
	<div class="result-right">
		<div class="innerright">
			<div class="socialtext">
			<p><a class="sociallink" target="_blank" 
			href="http://twitter.com/share?related=japanesegoi&via=japanesegoi&lang=[en]&text=I got a score of I donno :(&url=http://japanesegoi.com/vocabulary-quiz/<?php echo $category_variable; ?>"><img src="<?php echo bloginfo('template_directory') . "/images/TwitterLogo_Tweet.png"; ?>" alt="Tweet your results"/></a><span class="socmsg">Post your results to twitter.</span></p>
			</div>
			<div class="clear"></div>
		</div>
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
<?php } else echo "Sorry no category found"; ?>