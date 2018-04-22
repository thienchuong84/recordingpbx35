<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB; // if have, not use \DB::table('cdr').. and can use DB::table('cdr').. not need \
use Auth;
use App;
use App\Cdr;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('CheckUserIsActive');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        return view('home');
    }

    public function index(Request $request)
    {
        if($request->date) {
            // dd(Auth::user()->roles);
            $from = $request->from.' 00:00:00';
            $to = $request->to.' 23:59:59';
            $ext = trim($request->ext);
            $sortName = $request->sortName;
            $sortWith = $request->sortWith;
            $extProject = '57';

            $arrRoles = $this->check_roles(Auth::user()->roles);
            // dd($arrRoles);
            if($arrRoles == '-1')
            {
                return view('home.index');
            }
            
            $where = $this->build_role_condition($arrRoles);
            // dd($where);

            // if(strlen($ext) == 0) {
            //     $where = "( (cnum LIKE '".$extProject."%' AND LENGTH(dst)>5) OR (dst LIKE '".$extProject."%' AND LENGTH(cnum)>5) )";
            //     // $where = $this->build_role_condition($arrRoles);
            // }
            // else if(strlen($ext) <= 5) {
            //     $where = "(cnum='" . $ext . "' OR dst='". $ext ."')";

            //     // $where .= "AND cnum LIKE '".$extProject."%' AND dst LIKE '".$extProject."%'";
            //     // if use above condition, when search 57023, we will only see ext 57023 call 57xxx, not in 57023 call 1702 or difference call id
            //     // use below condition will see 57023 call 57xxx and 1xxx or 51xxx ...
            //     $where .= "AND cnum LIKE '".$extProject."%'";		
            // } 
            // else {
            //     $where = "(src LIKE '%".$ext."' OR dst LIKE '%".$ext."')";

            //     $where .= "AND cnum LIKE '".$extProject."%'";
            // }
            // dd($where);
            // dd(public_path('recording'));
            

            // $results = DB::table('cdr')->take(5)->get();
            // $results = App\Cdr::take(10)->orderBy('id','desc')->get();

            $results = App\Cdr::where('disposition', 'ANSWERED')
                ->where('billsec', '<>', 0)
                ->where('calldate', '>=', $from)
                ->where('calldate', '<=', $to)
                ->whereRaw($where)
                ->take(10)
                ->orderBy($sortName, $sortWith)
                ->paginate(50);


            return view('home.index', compact('results'));
        }
        return view('home.index');
    }

    public function download($name) {
        // dd($name);
        $fileNotFound = public_path('file_not_found.wav');
        if(file_exists(public_path('filesOnline/').$name)) {
            $pathToFile = public_path('filesOnline/').$name;     // on linux, need change \ to /
        }
        else if(file_exists(public_path('filesOffline/').$name)) {
            $pathToFile = public_path('filesOffline/').$name;
        }
        else {
            return response()->download($fileNotFound);
        }
        return response()->download($pathToFile);
    }

    public function play($name) {
        // $name = '20180307-090503-hsbc-out-91610913800186-57042-1520388302.2800.wav';
        $fileNotFound = public_path('file_not_found.wav');
        if(file_exists(public_path('filesOnline/').$name)) {
            $pathToFile = public_path('filesOnline/').$name;     // on linux, need change \ to /
        }
        else if(file_exists(public_path('filesOffline/').$name)) {
            $pathToFile = public_path('filesOffline/').$name;
        }
        else {
            return response()->download($fileNotFound);
        } 
        return response()->file($pathToFile);
    }

    public function downloadOffline($name) {
        $filename = 'temp-image.jpg';
        $tempImage = tempnam(sys_get_temp_dir(), $filename);
        copy('https://my-cdn.com/files/image.jpg', $tempImage);
        
        return response()->download($tempImage, $filename);        
    }  
    
	private function check_roles($roles){
		while(strpos($roles, "**")!==false){
			$roles = str_replace("**", "*", $roles);
		}
		$rarr = explode(",", $roles);
		$cnt = count($rarr);
		$darr = array();
		for ($i=0; $i<$cnt; $i++){
			$t = trim($rarr[$i]);
			if ($t=="*" || strpos($t, "**")!==false) return 1;
			if ($t!=""){
				$darr[] = str_replace('*', '%', $t);
			}
		}
		$cnt = count($darr);
		if ($cnt==0) return -1;
		return $darr;
    }    
    
	private function build_role_condition($roles, $src_field='cnum', $dst_field='dst'){
		$cnt = count($roles);
		$wheres = array();
		for($i=0; $i<$cnt; $i++){
			$wheres[] = sprintf("(%s like '%s')", $src_field, $roles[$i]);
			$wheres[] = sprintf("(%s like '%s')", $dst_field, $roles[$i]);
			$wheres[] = sprintf("(channel like 'SIP/%s-%%')", $roles[$i]);
		}
		return "(".implode(" OR ", $wheres).")";
    }    
    
    public function report_ob() {
        $exts = [];
        for($i = 1050; $i <= 1070; $i++) {
            $exts[] = $i;
        }

        return view('home.report_ob', compact('exts'));
    }

    public function report_ob_get(Request $request) {
        $from = $request->from;
        $to   = $request->to;
        $exts = $request->exts;
        return response()->json([
            // 'all'   => $input,
            'exts'  => $exts
        ]);
    }

    // refer test code from TestController@report_ob_post2
    public function report_ob_post(Request $request) {
        $from = $request->from.' 00:00:00';
        $to = $request->to.' 23:59:59';
        $arrExts = $request->exts;  // trên html, name="exts[]" , vì thế khi submit lên nó là 1 array
        $extProject = '57';

        $strExts = "";
        foreach($arrExts as $ext) {
            $strExts .= $ext . ',';
        }
        $strExts = substr($strExts, 0, strlen($strExts) - 1);
        
        // hàm check_roles đc khai báo trong app/helpesChuong.php
        // trong composer.json , có khai báo trong autoload file này
        // khi cập nhật trong composer.json, cần đánh thêm lệnh composer dump-autoload để cập nhật
        $arrRoles = check_roles(Auth::user()->roles);
        // // dd($arrRoles);

        if($arrRoles == '-1')
        {
            return response()->json([
                'msgErr'    => 'Error with arrRoles'
            ]);
        }
        
        $where = build_role_condition($arrRoles);   

        // select from select , build from raw sql
        /*
            SELECT 
                obsum.Exten,
                obsum.Total_Dial_Call,
                obsum.Total_Time, 
                (obsum.Total_Time DIV obsum.Total_Dial_Call) AS Avg_Time_Call,
                obsum.Total_Connect_Call,
                obsum.Total_Wait_Time,
                obsum.Total_Talk_Time,
                (obsum.Total_Talk_Time DIV obsum.Total_Connect_Call) AS Avg_ConnectTime_Call
            FROM 
            (SELECT 
                cnum AS Exten, 
                COUNT(id) AS Total_Dial_Call,
                SUM(duration) AS Total_Time,	
                SUM(duration-billsec) AS Total_Wait_Time,
                SUM(billsec) AS Total_Talk_Time,
                SUM(CASE WHEN disposition = 'ANSWERED' THEN 1 ELSE 0 END) AS Total_Connect_Call
            FROM cdr
            WHERE cnum IN (1054,1056) AND calldate >= '2018-04-11 00:00:00' AND calldate <= '2018-04-12 23:59:59'
            GROUP BY cnum) obsum
        */

        $subQuery = Cdr::selectRaw('cnum AS Exten')
            ->selectRaw('COUNT(id) AS Total_Dial_Call')
            ->selectRaw('SUM(duration) AS Total_Time')
            ->selectRaw('SUM(duration-billsec) AS Total_Wait_Time')
            ->selectRaw('SUM(billsec) AS Total_Talk_Time')
            ->selectRaw("SUM(CASE WHEN disposition = 'ANSWERED' THEN 1 ELSE 0 END) AS Total_Connect_Call")
            ->where('calldate', '>=', $from)
            ->where('calldate', '<=', $to)
            ->whereRaw('cnum in ('.$strExts.')')
            ->groupBy('cnum');
        // $subQueryResult = $subQuery->get();  // test

        $query = DB::table( DB::raw("({$subQuery->toSql()}) as obsum"))
            ->mergeBindings($subQuery->getQuery())
            ->selectRaw('obsum.Exten AS Exten')
            ->selectRaw('obsum.Total_Dial_Call AS Total_Dial_Call')
            ->selectRaw('obsum.Total_Time AS Total_Time')
            ->selectRaw('(obsum.Total_Time DIV obsum.Total_Dial_Call) AS Avg_Time_Call')
            ->selectRaw('obsum.Total_Connect_Call AS Total_Connect_Call')
            ->selectRaw('obsum.Total_Wait_Time AS Total_Wait_Time')
            ->selectRaw('obsum.Total_Talk_Time AS Total_Talk_Time')
            ->selectRaw('(obsum.Total_Talk_Time DIV obsum.Total_Connect_Call) AS Avg_ConnectTime_Call')
            ->get();

        return response()->json([
            'query'     => $query
        ]);         
    }
}
