<?php namespace Iome\Http\Controllers\Admin;

use Iome\Http\Controllers\Controller;
use Iome\Extension;
use Iome\Organization;
use Iome\User;

class DashboardController extends Controller
{

	public function index()
	{
		$title = "Dashboard";

		$exts = Extension::count();
		$orgs = Organization::count();
		$users = User::count();

		return view( 'admin.dashboard.index', compact( 'exts', 'orgs', 'title', 'users' ) );
	}
}