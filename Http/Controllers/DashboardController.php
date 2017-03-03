<?php namespace Modules\Base\Http\Controllers;

/**
 * Class DashboardController
 * @package Modules\Base\Http\Controllers\Backend
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = access()->user();
        return view('dashboard', compact('user'));
    }
}