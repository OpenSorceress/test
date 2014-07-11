<?php
/**
 * Created by PhpStorm.
 * Because[$reason].
 *
 * User: leah
 * Date: 7/10/14
 * Time: 1:26 PM
 */


class Heap extends SplHeap {

	public static $arr;
	public $set = array();
	public $results = 0;
	public $max;

	/**
	 * This class is invoked by Gearman workers running concurrent tasks that pass in a dynamically determined range for the heap.
	 * This drastically improves performance.
	 *
	 * @param string $input
	 */
	public function __construct($input) {

		$input = json_decode($input, true);

		if (!isset(self::$arr) && !empty($input)) {
			self::$arr = new SplFixedArray(count($input['arr']));
			foreach($input['arr'] as $key=>$val) {
				self::$arr[$key] = $val;
			}
		}
		// as # -> infinity, E(X^2)/(2) approaches X^2
		$this->max = pow(2, self::$arr->count());
		print_r("range: ".  $input['start'] . " " . $input['end'] . "\n\r");
		$this->run($input['start'], $input['end']);
	}

	/**
	 * Dynamically assemble the subsets. Uses base binary integers as matrices.
	 *
	 * @param $pos
	 * @return array
	 */
	public function set($pos) {
		$set = array();
 		for($i = 0; $i < strlen(decbin($pos)); $i++) {
			$key = strlen(decbin($pos)) - ( $i + 1 );
			if(decbin($pos)[$key] == 1)
				$set[] = self::$arr[$i];
		}
		return $set;
	}

	/**
	 * Hijacking the SplHeap::compare method to evaluate validity of the sum
	 *
	 * @param mixed $target
	 * @param mixed $set
	 * @return bool|int
	 */
	public function compare($target, $set) {
		$sum = 0;
		while(list(,$val) = each($set)) {
			if ($val != $target) {
				$sum += $val;
			}
		}
		return $target == $sum;
	}

	/**
	 *
	 *
	 * @param $start
	 * @param $end
	 * @return string
	 */
	public function run($start, $end) {

		print_r("count: ". self::$arr->getSize()."\n\r");
		print_r("max: " . $this->max . "\n\r");
		print_r("range start: " . $start. "\n\r");
		print_r("range end: " . $end . "\n\r");

		for($n = $start; $n < $end; $n++) {

			$set = $this->set($n);

			$target = array_pop($set);

			if( count($set) > 0 && $this->compare($target, $set) ) {
				$this->set[$target][] = $set;
				$this->results++;
				print_r("sum: " . $target . "\n\r");
				print_r("n: " . $n . "\n\r");
				print_r("-----\n\r");

			}

		}
		return json_encode($this->set);
	}

}
