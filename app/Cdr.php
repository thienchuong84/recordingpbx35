<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{
    protected $table = "cdr";

    // muốn lấy các biến trong cdr table ta dùng $this->attributes['']
    // nên ta có thể khai báo nó như là 1 biến của model cdr

    public function startDate() {
        return substr($this->attributes['calldate'], 0, 10);
    }

    // start time by function
    public function startTime() {
        return substr($this->attributes['calldate'], 11);
    }

    // end time
    public function endTime() {
        return date('H:i:s', strtotime($this->startTime())+$this->billsec);
    }

    // return time 70s -> 00:01:10
	function show_time(){
        $second = $this->attributes['billsec'];
		$h=0; $m=0; $s=0;
		if ($second>0){
			$s = fmod($second, 60);
			$min = floor($second/60);
			if ($min>0){
				$m = fmod($min, 60);
				$h = floor($min/60);
			}
		}
		return sprintf("%02d:%02d:%02d", $h, $m, $s);
	}    
}
