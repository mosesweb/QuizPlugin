
		jQuery(function($) { // DOM is now ready and jQuery's $ alias sandboxed

			
		var num = 1;
		var correct = 0;
		var totalcorrect = 0;
		var wronganswers = 0;
		var totalwronganswers = 0;
		var corrArr = [];
		var mistakesArr = [];

		$.fn.shuffle = function() {
 
			var allElems = this.get(),
				getRandom = function(max) {
					return Math.floor(Math.random() * max);
				},
				shuffled = $.map(allElems, function(){
					var random = getRandom(allElems.length),
						randEl = $(allElems[random]).clone(true)[0];
					allElems.splice(random, 1);
					return randEl;
			   });
	 
			this.each(function(i){
				$(this).replaceWith($(shuffled[i]));
			});
	 
			return $(shuffled);
	 
		};

		$('body').on('click','.repeat-afterquiz',function()
		{
			firstAgain();	
		});
		function firstAgain()
		{

			$(".result").hide();
			$(".correct-box .correct").text("0");
			$(".wrong-box .wrong").text("0");
			$(".result-info-text").html("");
			$(".answer-options").hide();

			// randomize elements!
			
		

			if($('.vocabmode').text() == 'random')
			{
				//$('.answer-options').shuffle();
				/* other */
				var parent = $(".main_questionsarea");
				var divs = $(".main_questionsarea .answer-options");
				while (divs.length) {
					parent.append(divs.splice(Math.floor(Math.random() * divs.length), 1)[0]);
				}
				/* end other */
				var newposdivs = $('.answer-options');
				var x = 0;
				$( newposdivs ).each(function( index ) {
					var question_number = index+1;
					var boxclassname = $(this).attr('class').split(' ')[1];

					$(this).removeClass(function (index, className) {
						return (className.match (/(^|\s)box-\S+/g) || []).join(' ');
					});

					$(this).find('.current-q').text(question_number);
					$(this).addClass('box-' + question_number);
				
				});
			}
			$(".main_questionsarea").show();
			$(".answer-options.box-1").show();
			$(".result-info-word-info").text("");

			num = 1;
			correct = 0;
			wronganswers = 0;
			corrArr = [];
			mistakesArr = [];
		}

		function LastReached(corr, mist, ending_question_num)
		{
			var focus_el = $(".answer-options.box-" + ending_question_num).children(".question");

			var totalnums = focus_el.find('span.levels-q').first().text();
			var corrproc = Math.floor((corr/totalnums) * 100);
			var ele = $('.answered');

			registerScore(corr, mist, totalnums, corrproc);
			// Show score if finished
			// todo logic if level or map is finished.
			$('.main_questionsarea').hide();
			
				//alert("ye last");
				var goodorbad = "Not the greatest score of all time but you did try.";
				if(corrproc > 40)
				{
					goodorbad = "A little bit more practice wouldn't hurt! Nonetheless good efforts.";
				}
				if(corrproc > 50)
				{
					goodorbad = "You're on the right track. Being over the 50% is not bad.";
				}
				if(corrproc > 60)
				{
					goodorbad = "You pass! You are over 60% which is good!";
				}
				if(corrproc > 70)
				{
					goodorbad = "You are good at this. ";
				}
				if(corrproc > 80)
				{
					goodorbad = "Almost perfection reached. Just a little bit more and you'll get over 90%.";
				}
				if(corrproc > 90)
				{
					goodorbad = "Wonderful efforts. Really great job.";
				}
				if(corrproc == 100)
				{
					goodorbad = "EXCELLENT.";
				}
				$('.result-header').text("You got " + corr + " correct answers out of "+ totalnums + ". " + goodorbad);
				$('.result-header').append("<span class='next-level'>next level</span>");
				$('.result-text').text("Correct procentage: " + corrproc + "%");
				$("a.sociallink").attr("href", "http://twitter.com/share?related=japanesegoi&via=japanesegoi&lang=[en]&text=I got " + corrproc+ "%25 score in Japanese Vocabulary Quiz: <?php echo $current_catname ?>.&url=http://japanesegoi.com/vocabulary-quiz/<?php echo $whatcategory . "/" . $mode; ?>");

				$('.result').fadeIn(600);
				$('.next-level').click(function()
				{
					$('.result').hide();
					$('.main_questionsarea').show();
				});
				
			
			console.log($('span.total-q').text());

		}
		function MapComplete(corr, mist)
		{
			var totalnums = $('span.total-q').first().text();
			var corrproc = Math.floor((corr/totalnums) * 100);
			var ele = $('.answered');

			// special register?
			registerScore(corr, mist, totalnums, corrproc);
			alert("you did it! map complete.")
			$('.main_questionsarea').hide();
				var goodorbad = "Not the greatest score of all time but you did try.";
				if(corrproc > 40)
				{
					goodorbad = "A little bit more practice wouldn't hurt! Nonetheless good efforts.";
				}
				if(corrproc > 50)
				{
					goodorbad = "You're on the right track. Being over the 50% is not bad.";
				}
				if(corrproc > 60)
				{
					goodorbad = "You pass! You are over 60% which is good!";
				}
				if(corrproc > 70)
				{
					goodorbad = "You are good at this. ";
				}
				if(corrproc > 80)
				{
					goodorbad = "Almost perfection reached. Just a little bit more and you'll get over 90%.";
				}
				if(corrproc > 90)
				{
					goodorbad = "Wonderful efforts. Really great job.";
				}
				if(corrproc == 100)
				{
					goodorbad = "EXCELLENT.";
				}
				$('.result-header').text("You got " + corr + " correct answers out of "+ totalnums + ". " + goodorbad);
				$('.result-text').text("Correct procentage: " + corrproc + "%");
				$("a.sociallink").attr("href", "http://twitter.com/share?related=japanesegoi&via=japanesegoi&lang=[en]&text=I got " + corrproc+ "%25 score in Japanese Vocabulary Quiz: <?php echo $current_catname ?>.&url=http://japanesegoi.com/vocabulary-quiz/<?php echo $whatcategory . "/" . $mode; ?>");

				$('.result').fadeIn(600);
				$('.next-level').click(function()
				{
					$('.result').hide();
					$('.main_questionsarea').show();
				});
				
			
			console.log($('span.total-q').text());
		}
		function registerScore(corr, mist, totalq, proctotal)
		{
			var levelid = $('#level-id').text();
			var lamountofq = totalq;
			var theproctotal = proctotal;

			  $.ajax({
			  url: '/wp-content/plugins/simple-yet-powerful-quiz/ajax/checkanswerv2.php',
			  type: 'post',
			  data: { ca: corr, ma: mist, lcategory: levelid, lamountofq: lamountofq, proctotal: theproctotal },
			  success: function(data) {
				console.log(data);
			  }
			});
		}

		var totalq = $('span.total-q').first().text();
		var tcatname = $('.vocabname').text();
		var cq = $('.current-q').text();
		 function runAjax(squestion, sanswer, levelnum, thenum, current_level)
		 {
			  $.ajax({
			  url: '/wp-content/plugins/simple-yet-powerful-quiz/ajax/checkanswerv2.php',
			  type: 'post',
			  data: { 
				whatcategory: tcatname, 
				whatquest: squestion, 
				answer: sanswer, 
				mode: 'meaning', 
				qnumber: levelnum,
				current_level: current_level
			},
			  success: function(data, status) {
				//console.log( "Sample of data:" + data + correct);
				var obj = jQuery.parseJSON(data);
				console.log(obj);
				console.log(data);
				if(obj.result == 'correct')
				{
					correct++;
					totalcorrect++;
					$(".correct").text(correct);
					$('.result-info-text').html(squestion + ': <span class="correctindicate">' + obj.guess + '</span> was <span class="correctindicate">correct</span>!');
					corrArr.push(obj.guess);
					$('.correct-info').text(corrArr.toString());
				}
				if(obj.result == 'wrong')
				{
					wronganswers++;
					totalwronganswers++;
					$('.wrong').text(wronganswers);
					$('.result-info-text').html(squestion + ': ' + '<span class="wrongindicate">' + obj.guess + '</span> was <span class="wrongindicate">wrong</span>. Correct answer is:  <span class="correctindicate">' + obj.japanese + '</span>.');
					mistakesArr.push(obj.guess);
					$('.wrong-info').text(mistakesArr.toString());
				}
				if(obj.kanji != '')
				{
					$('.result-info-word-info').html('<b>' + obj.japanese + ' (' + obj.romaji + ')');
				}
				else
				{
					$('.result-info-word-info').html('<b>' + obj.romaji + '</b>');
				}
				if(obj.is_last_question)
				{
					LastReached(correct, wronganswers, thenum);
					correct = 0;
					wronganswers = 0;
				}
				if(thenum == totalq)
				{
					MapComplete(totalcorrect, totalwronganswers)
					totalcorrect = 0;
					totalwronganswers = 0;
				}
			  },
			  error: function(xhr, desc, err) {
				console.log(xhr);
				console.log("Details: " + desc + "\nError:" + err);
			  }
			}); // end ajax call
			
		 }
		 
		 
		$(".answer-options").hide();
		$(".answer-options.box-" + num).show();
		
			function checkClass()
			{
				if($( ".answer" ).hasClass( "selected" ))
				{
					//alert("yes");
				}
			}
			$(".answer").click(function()
			{
				$(this).addClass("selected");
				checkClass();
				var q = $(this).closest(".answer-options.box-" + num).children(".question").find('.questionword').text();
				var a = $(this).text();

				var current_level = $(this).closest(".answer-options.box-" + num).children(".question").find('.current_level').text();
				
				if(current_level)
				console.log(current_level);

				//alert(q + a);
				var level_question_num = 0;
				level_question_num = $(this).closest(".answer-options.box-" + num).children(".question").find('.current-q').text();


				runAjax(q, a, level_question_num, num, current_level);
				
				// done. hide this
				$(".answer-options.box-" + num).hide();
				$(".answer-options.box-" + num).addClass("answered");
				num++;
				// Show next number of that class
				$(".answer-options.box-" + num).show();
			});
		});
		