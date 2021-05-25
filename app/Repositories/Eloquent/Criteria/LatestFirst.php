<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;



class LatestFirst implements CriterionInterface

{
	public function apply($entity)

	{
		return $entity->orderBy('position');
	}
}

