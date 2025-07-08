<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConnectionController extends Controller
{
    public function index() {
        try {
           $dbconnect = DB::connection('SSI')->getPDO();
           $dbname = DB::connection('SSI')->getDatabaseName();
           echo "Connected successfully to the database. Database name is :".$dbname;
        } catch(Exception $e) {
           echo "Error in connecting to the database";
        }
     }
}
