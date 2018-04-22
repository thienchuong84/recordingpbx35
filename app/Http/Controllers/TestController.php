<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB; // if have, not use \DB::table('cdr').. and can use DB::table('cdr').. not need \
use Auth;
use App;
use App\Cdr;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('CheckUserIsActive');
    }

    public function report_ob_post2(Request $request) {
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

        $subQueryResult = $subQuery->get();
        // $results = App\Cdr::where('disposition', 'ANSWERED')
        // ->where('billsec', '<>', 0)
        // ->where('calldate', '>=', $from)
        // ->where('calldate', '<=', $to)
        // ->whereRaw($where)
        // ->take(10)
        // ->orderBy($sortName, $sortWith)
        // ->paginate(50);

        $dataSet = [
            [ "Tiger Nixon", "System Architect", "Edinburgh", "5421", "2011/04/25", "$320,800" ],
            [ "Garrett Winters", "Accountant", "Tokyo", "8422", "2011/07/25", "$170,750" ],
        ];

        return response()->json([
            // 'all'   => $input,
            'dataSet'   => $dataSet,
            //'arrRoles'     => $arrRoles,
            //'where'     => $where,
            //'strExts'   => $strExts,
            'subQuery'  => $subQuery->getQuery(),
            'subQueryResule' => $subQueryResult,
            'query'     => $query
        ]);         
    }

    public function report_ob_post(Request $request) {
        $dataSet = [
            [ "Tiger Nixon", "System Architect", "Edinburgh", "5421", "2011/04/25", "$320,800" ],
            [ "Garrett Winters", "Accountant", "Tokyo", "8422", "2011/07/25", "$170,750" ],
            [ "Ashton Cox", "Junior Technical Author", "San Francisco", "1562", "2009/01/12", "$86,000" ],
            [ "Cedric Kelly", "Senior Javascript Developer", "Edinburgh", "6224", "2012/03/29", "$433,060" ],
            [ "Airi Satou", "Accountant", "Tokyo", "5407", "2008/11/28", "$162,700" ],
            [ "Brielle Williamson", "Integration Specialist", "New York", "4804", "2012/12/02", "$372,000" ],
            [ "Herrod Chandler", "Sales Assistant", "San Francisco", "9608", "2012/08/06", "$137,500" ],
            [ "Rhona Davidson", "Integration Specialist", "Tokyo", "6200", "2010/10/14", "$327,900" ],
            [ "Colleen Hurst", "Javascript Developer", "San Francisco", "2360", "2009/09/15", "$205,500" ],
            [ "Sonya Frost", "Software Engineer", "Edinburgh", "1667", "2008/12/13", "$103,600" ],
            [ "Jena Gaines", "Office Manager", "London", "3814", "2008/12/19", "$90,560" ],
            [ "Quinn Flynn", "Support Lead", "Edinburgh", "9497", "2013/03/03", "$342,000" ],
            [ "Charde Marshall", "Regional Director", "San Francisco", "6741", "2008/10/16", "$470,600" ],
            [ "Haley Kennedy", "Senior Marketing Designer", "London", "3597", "2012/12/18", "$313,500" ],
            [ "Tatyana Fitzpatrick", "Regional Director", "London", "1965", "2010/03/17", "$385,750" ],
            [ "Michael Silva", "Marketing Designer", "London", "1581", "2012/11/27", "$198,500" ],
            [ "Paul Byrd", "Chief Financial Officer (CFO)", "New York", "3059", "2010/06/09", "$725,000" ],
            [ "Gloria Little", "Systems Administrator", "New York", "1721", "2009/04/10", "$237,500" ],
            [ "Bradley Greer", "Software Engineer", "London", "2558", "2012/10/13", "$132,000" ],
            [ "Dai Rios", "Personnel Lead", "Edinburgh", "2290", "2012/09/26", "$217,500" ],
            [ "Jenette Caldwell", "Development Lead", "New York", "1937", "2011/09/03", "$345,000" ],
            [ "Yuri Berry", "Chief Marketing Officer (CMO)", "New York", "6154", "2009/06/25", "$675,000" ],
            [ "Caesar Vance", "Pre-Sales Support", "New York", "8330", "2011/12/12", "$106,450" ],
            [ "Doris Wilder", "Sales Assistant", "Sidney", "3023", "2010/09/20", "$85,600" ],
            [ "Angelica Ramos", "Chief Executive Officer (CEO)", "London", "5797", "2009/10/09", "$1,200,000" ],
            [ "Gavin Joyce", "Developer", "Edinburgh", "8822", "2010/12/22", "$92,575" ],
            [ "Jennifer Chang", "Regional Director", "Singapore", "9239", "2010/11/14", "$357,650" ],
            [ "Brenden Wagner", "Software Engineer", "San Francisco", "1314", "2011/06/07", "$206,850" ],
            [ "Fiona Green", "Chief Operating Officer (COO)", "San Francisco", "2947", "2010/03/11", "$850,000" ],
            [ "Shou Itou", "Regional Marketing", "Tokyo", "8899", "2011/08/14", "$163,000" ],
            [ "Michelle House", "Integration Specialist", "Sidney", "2769", "2011/06/02", "$95,400" ],
            [ "Suki Burks", "Developer", "London", "6832", "2009/10/22", "$114,500" ],
            [ "Prescott Bartlett", "Technical Author", "London", "3606", "2011/05/07", "$145,000" ],
            [ "Gavin Cortez", "Team Leader", "San Francisco", "2860", "2008/10/26", "$235,500" ],
            [ "Martena Mccray", "Post-Sales support", "Edinburgh", "8240", "2011/03/09", "$324,050" ],
            [ "Unity Butler", "Marketing Designer", "San Francisco", "5384", "2009/12/09", "$85,675" ]
        ];
        

        return response()->json([
            // 'all'   => $input,
            'dataSet'  => $dataSet
        ]);        
    }
}
