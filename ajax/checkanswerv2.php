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
	$catgroup = $simpleplug->prefixTableName('goicatgroup');
	$catwordtable = $simpleplug->prefixTableName('goiwordcategories');
	$resulttable = $simpleplug->prefixTableName('goiresults');
	$categoryQuizId = 0;
	$total_levels = 0;
	$questions_in_level = [];

	//" . $question . "' AND japanese = '" . $answer . "'";
	if(isset($question) && isset($answer) && isset($category))
	{
		// must fix japanese utf
		$wpdb->set_charset($wpdb->dbh,"utf8");
		$question = $wpdb->_real_escape($question);
		$answer = $wpdb->_real_escape($answer);
		$category = $wpdb->_real_escape($category);
		$wpdb->show_errors( true );

		$countQuestionsSQL = "
		SELECT COUNT(*) AS count, $cattable.id AS categoryquizid FROM $wordtable
		INNER JOIN $catwordtable ON $wordtable.id = $catwordtable.word_id
		INNER JOIN $cattable ON $cattable.id = $catwordtable.category_id
		INNER JOIN $catgroup ON $catgroup.id = $cattable.group_id
		WHERE 
		$catgroup.slug_name = '". $category ."'";
		$numberOfQResult = $wpdb->get_results( $countQuestionsSQL, ARRAY_A);
		$numberofQuestions = 0;
		
		$levelsCountSQL = "
		SELECT DISTINCT COUNT($cattable.id) AS levels_count
		FROM $cattable
		
		INNER JOIN $catgroup ON $catgroup.id = $cattable.group_id
		
		WHERE 
		$catgroup.slug_name = '". $category ."'";
		$numberOfLevelsResult = $wpdb->get_results( $levelsCountSQL, ARRAY_A);
		if($wpdb->num_rows > 0)
		{
			$total_levels = $numberOfLevelsResult[0]["levels_count"];
		}

		// TODO OPTIMISE EVERYTHING IN THIS FILE..
		$countQuestionsSQL = "
		SELECT COUNT(*) AS count, $cattable.id AS categoryquizid FROM $wordtable
		INNER JOIN $catwordtable ON $wordtable.id = $catwordtable.word_id
		INNER JOIN $cattable ON $cattable.id = $catwordtable.category_id
		INNER JOIN $catgroup ON $catgroup.id = $cattable.group_id
		WHERE 
		$catgroup.slug_name = '". $category ."'";
		$numberOfQResult = $wpdb->get_results( $countQuestionsSQL, ARRAY_A);
		$numberofQuestions = 0;
		
		$LevelsInfoSQL = "
		SELECT 
		COUNT($wordtable.id) AS words_in_level, 
		$cattable.name AS level_name, 
		$catgroup.name AS map_name 
		FROM $wordtable
		INNER JOIN `$catwordtable` 
		ON $wordtable.id = $catwordtable.word_id 
		INNER JOIN $cattable 
		ON $cattable.id = $catwordtable.category_id 
		INNER JOIN $catgroup ON 
		$cattable.group_id = $catgroup.id 
		WHERE $catgroup.slug_name = '$category'
		GROUP BY $cattable.name
		";

		$levelsInfoResult = $wpdb->get_results( $LevelsInfoSQL, ARRAY_A);
		if($wpdb->num_rows > 0)
		{
			$questions_in_level = $levelsInfoResult;
		}


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
			INNER JOIN $catgroup ON $catgroup.id = $cattable.group_id
			WHERE 
			$catgroup.slug_name = '". $category ."' AND meaning = '" . $question . "' AND japanese = '" . $answer . "'";
			
			$sql2 = "SELECT * FROM $wordtable WHERE meaning = '" . $question . "' LIMIT 1";
			break;
		}
		
		
		// Array and then make json encode
		$arr = array('result' => 'correct', 
		'correct_answer' => '', 
		'wrong_answer' => '', 
		'last_question' => '', 
		'guess' => $answer, 'japanese' => '', 
		'kana' => '', 
		'kanji' => '', 
		'romaji' => '', 
		'meaning' => '');

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
			$result2 = $wpdb->get_results( $sql2, ARRAY_A);	
			$arr['result'] = 'wrong';
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
		$arr['userid'] = get_current_user_id();
		$current_level = 0;
		if(isset($_POST['current_level']))
		{
			$current_level = $_POST['current_level'];
			$arr['current_level'] = $_POST['current_level'];
		
		}	
		$arr['levels_count'] = $total_levels;
		
		$arr['current_question'] =  $currentquestionnum;
		$arr['questions_in_level'] =  $questions_in_level;
		$arr['number_of_questions'] = $numberofQuestions;
		$arr['number_of_questions_in_level'] = $levelsInfoResult[$current_level-1]["words_in_level"];
		$arr['is_last_question'] = $levelsInfoResult[$current_level-1]["words_in_level"] == ($currentquestionnum) ? true : false;
		
		
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
	$catgroup = $simpleplug->prefixTableName('goicatgroup');
	$catwordtable = $simpleplug->prefixTableName('goiwordcategories');
	$resulttable = $simpleplug->prefixTableName('goiresults');
	$categoryid = null;
	
	$numberofQuestions = 0;
	
	$totalqs = 0;
	//$score = ($correctanswersgiven/$lamountofq)*100;
	$sqlloguserscore = "INSERT INTO `$resulttable` (`id`, `goicategory_id`, `procent_correctness`, `message`, `user_id`) VALUES (NULL, '$lcategory', '$proctotal', 'Correct: $correctanswersgiven', '".get_current_user_id()."');";
	$wpdb->query($sqlloguserscore);
	echo $sqlloguserscore;
}

}
$checkObj = new SimpleYetPowerfulQuiz_CheckAnswer();

if(isset($_POST['whatquest']))
{
	$question = $_POST['whatquest'];
	$category = $_POST['whatcategory'];
	$answer = $_POST['answer'];
	$whatmode = $_POST['mode'];
	$qnumber = $_POST['qnumber'];

	$checkObj->CheckAnswer($question, $category, $answer, $whatmode, $qnumber);
}
if(isset($_POST['ca']))
	$correctanswersgiven = $_POST['ca'];

if(isset($_POST['ma']))
	$mistakeanswersgiven = $_POST['ma'];

if(isset($_POST['lcategory']))
	$lcategory = $_POST['lcategory'];

if(isset($_POST['lamountofq']))
	$lamountofq = $_POST['lamountofq'];

if(isset($_POST['proctotal']))
	$proctotal = $_POST['proctotal'];

//ca: corr, ma: mist, lcategory: tcatname, lamountofq: lamountofq, proctotal: theproctotal

if(isset($correctanswersgiven) && isset($mistakeanswersgiven) && isset($lcategory) && isset($proctotal))
{
	$checkObj->LogResult($correctanswersgiven, $mistakeanswersgiven, $lcategory, $lamountofq, $proctotal);
}
?>