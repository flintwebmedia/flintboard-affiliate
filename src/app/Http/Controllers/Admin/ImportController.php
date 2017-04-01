<?php

namespace FlintWebmedia\FlintboardAffiliate\app\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportController extends Controller
{
    
    public function index() {
        return view('admin.import.index');
    }
    
}
