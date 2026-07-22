<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TutorController extends Controller
{
    public function index()    { return redirect()->route('admin.dashboard.home'); }
    public function create()   { return redirect()->route('admin.dashboard.home'); }
    public function store(Request $r) { return redirect()->route('admin.dashboard.home'); }
    public function show($id)  { return redirect()->route('admin.dashboard.home'); }
    public function edit($id)  { return redirect()->route('admin.dashboard.home'); }
    public function update(Request $r, $id) { return redirect()->route('admin.dashboard.home'); }
    public function destroy($id) { return redirect()->route('admin.dashboard.home'); }
    public function massDestroy(Request $r) { return redirect()->route('admin.dashboard.home'); }
}
