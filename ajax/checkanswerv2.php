<?php
include( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
include_once('../SimpleYetPowerfulQuiz_Plugin.php');

class SimpleYetPowerfulQuiz_CheckAnswer extends SimpleYetPowerfulQuiz_Plugin {

	public function CheckAnswer($question, $category, $answer, $whatmode, $currentquestionnum)
	{

	$simpleplug = new SimpleYetPowerfulQuiz_Plugin();
	global $wpdb;
	$wordtable = $simpleplug->prefixTableName('goiword');
	$cattable = $simpleplug->prefixTableName('goicategories');
	$catwordtable = $simpleplug->prefixTableName('goiwordcategories');
	$resulttable = $simpleplug->prefixTableName('goiresults');
	$categoryQuizId = 0;

	//" . $question . "' AND japanese = '" . $answer . "'";
	if(isset($question) && isset($answer) && isset($category))
	{
		// Connect to DB
		$mysqli = new mysqli("", ", "", "");
		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}
		// must fix japanese utf
		mysqli_set_charset($mysqli,"utf8");
		$question = $mysqli->real_escape_string($question);
		$answer = $mysqli->real_escape_string($answer);
		$category = $mysqli->real_escape_string($category);

		$countQuestionsSQL = "
		SELECT COUNT(*) AS count, $cattable.id AS categoryquizid FROM $wordtable
		INNER JOIN $catwordtable ON $wordtable.id = $catwordtable.word_id
		INNER JOIN $cattable ON $cattable.id = $catwordtable.category_id
		WHERE 
		$cattable.slug_name = '". $category ."'";
		$numberOfQResult = $wpdb->get_results( $countQuestionsSQL, ARRAY_A);
		$numberofQuestions = 0;
		foreach($numberOfQResult as $c)
		{
			$numberofQuestions = $c["count"];
			$categoryQuizId = $c["categoryquizid"];
		}

		// What query to play with
		switch($whatmode)
		{
			case 'meaning':
			$sql = "
			SELECT * FROM $wordtable
			INNER JOIN $catwordtable ON $wordtable.id = $catwordtable.word_id
			INNER JOIN $cattable ON $cattable.id = $catwordtable.category_id
			WHERE 
			$cattable.slug_name = '". $category ."' AND meaning = '" . $question . "' AND japanese = '" . $answer . "'";
			
			$sql2 = "SELECT * FROM $wordtable WHERE meaning = '" . $question . "' LIMIT 1";
			break;
		}
		
		
		// Array and then make json encode
		$arr = array('result' => 'correct', 'correct_answer' => '', 'wrong_answer' => '', 'last_question' => '', 'guess' => $answer, 'japanese' => '', 'kana' => '', 'kanji' => '', 'romaji' => '', 'meaning' => '');
		$arr['last_question'] = 'What is ' . $question . ' in Japanese?';
		$result = $wpdb->get_results( $sql, ARRAY_A);
		$numrows = $wpdb->num_rows;
		
		
		$arr['correct_answer'] = "";
		$arr['japanese'] = "";
		$arr['kana'] = "";
		$arr['kanji'] = "";
		$arr['romaji'] = "";
		$arr['meaning'] = "";

		if($numrows > 0)
		{
			$arr['result'] = 'correct';
			foreach($result as $correctrow)
			{
				//$corrrow = $result->fetch_array(MYSQLI_ASSOC);
				$arr['correct_answer'] = $correctrow['japanese'];
				$arr['japanese'] = $correctrow['japanese'];
				$arr['kana'] = $correctrow['kana'];
				$arr['kanji'] = $correctrow['kanji'];
				$arr['romaji'] = $correctrow['romaji'];
				$arr['meaning'] = $correctrow['meaning'];
			}
		}
		else
		{
			// select correct answer (where meaning is question)
			//$correct_result = $mysqli->query( $sql2);	
			$result2 = $wpdb->get_results( $sql2, ARRAY_A);	
			$arr['result'] = 'wrong';
			//$corrrow = $correct_result->fetch_array(MYSQLI_ASSOC);
			foreach($result2 as $corrrow)
			{
				$arr['correct_answer'] = $corrrow['japanese'];
				$arr['japanese'] = $corrrow['japanese'];
				$arr['kana'] = $corrrow['kana'];
				$arr['kanji'] = $corrrow['kanji'];
				$arr['romaji'] = $corrrow['romaji'];
				$arr['meaning'] = $corrrow['meaning'];
			}
		}
		$arr['extra_info'] = "hey.." . get_current_user_id() . $currentquestionnum . ' | ' . $numberofQuestions;

		echo json_encode($arr);
	}
	else
	{
		echo "nope!! $category $question $answer";
	}
}
public function LogResult($correctanswersgiven, $mistakeanswersgiven, $lcategory, $lamountofq, $proctotal)
{

	$simpleplug = new SimpleYetPowerfulQuiz_Plugin();
	global $wpdb;
	$wordtable = $simpleplug->prefixTableName('goiword');
	$cattable = $simpleplug->prefixTableName('goicategories');
	$catwordtable = $simpleplug->prefixTableName('goiwordcategories');
	$resulttable = $simpleplug->prefixTableName('goiresults');
	$categoryid = null;

	$lcategoryIDresult = $wpdb->get_results( "SELECT id FROM $cattable WHERE $cattable.slug_name = '$lcategory'", ARRAY_A);
	$numberofQuestions = 0;
	foreach($lcategoryIDresult as $cate)
	{
		$categoryid = $cate["id"];
	}
	$totalqs = 0;
	//$score = ($correctanswersgiven/$lamountofq)*100;
	$sqlloguserscore = "INSERT INTO ` $resulttable` (`id`, `goicategory_id`, `procent_correctness`, `message`, `user_id`) VALUES (NULL, '$categoryid', '$proctotal', 'hej', '".get_current_user_id()."');";
	$wpdb->query($sqlloguserscore);
	echo $sqlloguserscore;
	
}

}
$checkObj = new SimpleYetPowerfulQuiz_CheckAnswer();

$question = $_POST['whatquest'];
$category = $_POST['whatcategory'];
$answer = $_POST['answer'];
$whatmode = $_POST['mode'];
$qnumber = $_POST['qnumber'];


$correctanswersgiven = $_POST['ca'];
$mistakeanswersgiven = $_POST['ma'];
$lcategory = $_POST['lcategory'];
$lamountofq = $_POST['lamountofq'];
$proctotal = $_POST['proctotal'];

$checkObj->CheckAnswer($question, $category, $answer, $whatmode, $qnumber);
//ca: corr, ma: mist, lcategory: tcatname, lamountofq: lamountofq, proctotal: theproctotal
if(isset($correctanswersgiven) && isset($mistakeanswersgiven) && isset($lcategory) && isset($proctotal))
{
	$checkObj->LogResult($correctanswersgiven, $mistakeanswersgiven, $lcategory, $lamountofq, $proctotal);
}
?>