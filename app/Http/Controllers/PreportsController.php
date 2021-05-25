<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;
use App\Traits\ShadowInfo;

class PreportsController extends Controller
{
	use ShadowInfo;

	public function show()
	{
		$reports = App\Preport::getReport();
		$usage = App\Preport::Usages();
		$unfound = App\Preport::unFound();

		$agg = App\Preport::aggUsages();
		$titled = $this->titled();
		$unknown = $this->unknown();
		$erate = App\Preport::errorRate();

		$cans = array($reports,$usage,$unfound,$titled,$unknown);

		sort($cans);

		$page = end($cans);

		return view('preports.show', compact('reports','usage','agg','unknown','titled','unfound','erate','page'));
	}
}
