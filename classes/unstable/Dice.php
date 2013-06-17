<?php

	class Dice {
		function irc_encode($roll) {
			require_once "./classes/".VERSION."/Irc.php";

			$rs = "";
			$return_me = "";

			$sides = 0;
			foreach($roll as $r)
			{
				if(is_array($r)) {
					foreach($r as $v)
						if($v == $sides)
							$rs .= GREEN.$v.NORMAL.",";
						else if($v == 1)
							$rs .= RED.$v.NORMAL.",";
						else
							$rs .= $v.",";
				}
				else {
					if($rs != '') {
						$return_me = "[".rtrim($rs,",")."],";
						$rs = "";
					}

					list($_,$sides) = explode('d', $r);
				}
				if(strpos($rs, "[") !== false)
					$return_me .= $rs;
			}

			$return_me .= "[".rtrim($rs,",")."]";

			return $return_me;
		}

		function perform_roll($roll) {	
			require_once "./classes/".VERSION."/Irc.php";

			$rolled = 0;
			$dice_rolls = array('num' => array(), 'irc' => array() );

			while($rolled < $roll['qty'])
			{
				$r = mt_rand(1, $roll['sides']);
				$dice_rolls['num'][] = $r;

				$rolled++;
			}

			return $dice_rolls;
		}

		function roll($_GET) {

			require_once "./classes/".VERSION."/external/eos/eos.class.php";
			require_once "./classes/".VERSION."/external/eos/stack.class.php";
			require_once "./classes/".VERSION."/Irc.php";

			$regex = '/[^\+^\-^\^^\)^\(^\*^ ]*d[^\+^\-^\^^\)^\(^\*^ ]*/';
			preg_match_all($regex, $_GET['dice_string'], $dice, PREG_PATTERN_ORDER);

			$roll_results = array();
			$equation = $_GET['dice_string'];
			$dice = $dice[0];
			foreach($dice as $d)
			{
				$count = 1;
				$roll_result = 0; 
				$die = $d." ";
				$length = strlen($die);
				$phase = "qty";
				$accum = 0;
				$roll = array('qty' => 0, 'sides' => '0');

				for($i=0; $i<$length; $i++) {
					if(is_numeric($die[$i])) {
						$accum .= $die[$i];
					}
					else {
						if($die[$i] == 'd') {
							$roll['qty'] = $accum;
							$phase = 'sides';
							$accum = 0;
						}
						else if($roll['qty'] != 0 && $phase == 'sides') {
							$roll['sides'] = $accum;
							$phase = "?";
							$accum = 0;
						}
					}
				}

				$dice_rolls = $this->perform_roll($roll);
				$dice_rolls = $dice_rolls['num'];
				$roll_results[] = trim($die);
				$roll_results[] = $dice_rolls;
				$roll_result = array_sum($dice_rolls);


				$equation = preg_replace('/'.trim($die).'/', $roll_result, $equation, 1);
			}
			
			$eq = new eqEOS();
			$result = $eq->solveIF($equation);
			$formatted_string = '';
			if( isset($_GET['format']) && strtolower($_GET['format']) == 'irc') {
					
				$formatted_string = $_GET['dice_string'] . " = " . GREEN . $result . NORMAL . " | ".$this->irc_encode($roll_results)." | $equation | ";
			}
			else {
				$formatted_string = $_GET['dice_string'];
			}	
			echo json_encode(
				array(
					"string" => $formatted_string,
					"roll_results" => $roll_results,
					"equation" => $equation,
					"result" => $result
				)
			);
		}		
	}

?>
