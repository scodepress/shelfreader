<?php

namespace App\Repositories\Criteria;

use App\Repositories\Criteria\CriterionInterface;

interface CriterionInterface

{
	public function apply($entity);

}