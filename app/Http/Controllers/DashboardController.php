<?php namespace Iome\Http\Controllers;

use Iome\Http\Controllers\Controller;
use Iome\Extension;
use Iome\Organization;
use Iome\User;
use Nebula;

class DashboardController extends Controller {

	public function index()
	{
		$title = 'Dashboard';

		$response = Nebula::getTotals();
		$orgs     = array_get($response, 'totalOrgs', 0);
		$users    = array_get($response, 'totalUsers', 0);
		$exts     = array_get($response, 'totalSips', 0);

		return view('admin.dashboard.index', compact('title', 'orgs', 'users', 'exts'));
	}
}