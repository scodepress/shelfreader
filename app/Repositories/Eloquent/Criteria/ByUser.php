<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;



class ByUser implements CriterionInterface

{
	protected $userId;

	public function __construct($userId)

	{
		$this->userId = $userId;
	}

	public function apply($entity)

	{
		return $entity->where('user_id', $this->userId);
	}
}