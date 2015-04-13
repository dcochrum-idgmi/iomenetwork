<?php namespace Iome\Http\Controllers;

use Iome\Http\Controllers\Controller;
use Iome\Extension;
use Iome\Organization;
use Iome\User;

class DashboardController extends Controller {

	public function index()
	{
		$title = 'Dashboard';

		$exts  = 105;
		$orgs  = 5;
		$users = 3;

		return view('admin.dashboard.index', compact('exts', 'orgs', 'title', 'users'));
	}
}