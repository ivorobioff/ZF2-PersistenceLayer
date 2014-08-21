<?php
namespace Developer\PersistenceLayer\Query;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class PriorityHeap extends \SplHeap
{

	/**
	 * @param Property $property1
	 * @param Property $property2
	 * @return int|void
	 */
	protected function compare($property1, $property2)
	{
		$p1 = $property1->getOrderPriority();
		$p2 = $property2->getOrderPriority();

		if ($p1 < $p2) return 1;
		if ($p1 > $p2) return -1;

		return 0;
	}
}