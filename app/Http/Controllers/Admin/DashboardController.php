<?php namespace Iome\Http\Controllers\Admin;

use Iome\Http\Controllers\Controller;
use Iome\Extension;
use Iome\Office;
use Iome\User;

class DashboardController extends Controller
{

	public function index()
	{
		$title = "Dashboard";

		$exts = Extension::count();
		$offices = Office::count();
		$users = User::count();

		return view( 'admin.dashboard.index', compact( 'exts', 'offices', 'title', 'users' ) );
	}
}