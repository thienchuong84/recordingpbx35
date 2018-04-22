@extends('layouts.appChuong')

@section('content')
  <br>

  <p class="text-secondary"><span class="text-danger">Welcome to <i class="font-weight-bold">{{ Auth::user()->name }}</i></span> ( Monitor extension: {{ Auth::user()->roles }} )

  {!! Form::open(['url' => '/', 'method' => 'get', 'id' => 'frmSearch']) !!}
    <div class="form-inline">
      {{ Form::label('from', 'From:', ['class' => 'mb-2 mr-sm-2'])}}
      {{ Form::text('from', Request::input('from') ? Request::input('from') : date('Y-m-d'), ['class' => 'form-control mb-2 mr-sm-2', 'id' => 'from']) }}

      {{ Form::label('to', 'To:', ['class' => 'mb-2 mr-sm-2'])}}
      {{ Form::text('to', Request::input('to') ? Request::input('to') : date('Y-m-d'), ['class' => 'form-control mb-2 mr-sm-2', 'id' => 'to']) }}

      {{ Form::text('ext', Request::input('ext') ? Request::input('ext') : '',['class' => 'form-control mb-2 mr-sm-2', 'placeholder' => 'extension or cellphone', 'id' => 'ext']) }}

      {{ Form::submit('Search', ['class' => 'btn btn-primary mb-2 mr-sm-2', 'id' => 'btnSearch']) }}
      <!-- <a href="#" class="btn btn-primary" id="btnSearch">Search</a> -->

      {{ Form::hidden('date', date("Ymd-his")) }}
      {{ Form::hidden('sortName', 'id', ['id' => 'sortName']) }}
      {{ Form::hidden('sortWith', 'DESC', ['id' => 'sortWith']) }}
    </div>


  {!! Form::close() !!}  

  @if(!empty($results))
    <!-- 
      @php 
        //print_r($results) 
      @endphp 
    -->
  <table class="table">
    <thead class="thead-dark">
      <tr>
        <th scope="col">Agent</th>
        <th scope="col">Date</th>
        <!-- <th><b>Date</b> <i class="fa fa-fw fa-sort" id="sort name=sort" value=""></i></th> -->
        <th scope="col">Dial Number</th>
        <th scope="col">Call Start Time <i class="fa fa-fw fa-sort" onclick="sort('calldate')"></i></th>
        <th scope="col">Call End Time</th>
        <th scope="col">Status</th>
        <th scope="col">Unique ID</th>
        <th scope="col">Recording File</th>
        <th scope="col">Play File</th>
      </tr>
    </thead>
    <tbody>
      @foreach($results as $cdr)
      <tr>
        <th scope="row">{{ ($cdr->cnum != '') ? $cdr->cnum : $cdr->src }}</th>
        <td>{{$cdr->startDate()}}</td>
        <td>{{$cdr->dst}}</td>
        <td>{{$cdr->startTime()}}</td>
        <td>{{$cdr->endTime()}}</td>
        <td>{{$cdr->show_time()}}</td>
        <td>
          @if($cdr->disposition == "ANSWERED")
            <span class="font-weight-bold bg-success text-white">{{ $cdr->disposition }}</span>
          @else
            <span class="bg-dark text-white">{{ $cdr->disposition }}</span>
          @endif
        </td>
        <td>
          @if($cdr->recordingfile != '')
            <a href="/download/{{$cdr->recordingfile}}">Download</a>
          @else
            Not Recording File
          @endif
        </td>
        <td>
          @if($cdr->recordingfile != '')
            <a href="/play/{{$cdr->recordingfile}}" target="_blank">Play</a>
          @else
            Not Recording File
          @endif        
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{$results->appends([
      'from' => Request::input('from'),
      'to' => Request::input('to'),
      'ext' => Request::input('ext'),
      'date' => Request::input('date'),
      'sortName' => Request::input('sortName'),
      'sortWith' => Request::input('sortWith'),
      'service' => Request::input('service')
    ])->links()}}
  @endif

@endsection

@section('scripts')
  <!-- <script src="/js/jquery-ui-1.12.1/jquery-ui.min.js"></script> -->
  <script>
    function checkNull(o, n) {
      if(o.val() == '') {
        alert(n + ' không được để trống');
        o.focus();
        return false;
      }
      return true;
    }

    function compareDate(from, to) {
      if(new Date(from) <= new Date(to)) {
        alert('from nho hon to');
      }
      alert('from lon hon to');
    }

    function sort(value) {
      sortName = document.getElementById('sortName');
      sortName.value = value;
      sortWith = document.getElementById('sortWith');
      if(sortWith.value == 'DESC') {
        sortWith.value = 'ASC';
      }
      else {
        sortWith.value = 'DESC';
      }
      document.getElementById('btnSearch').click();
    }

    $(document).ready(function(){
      $from = $('#from');
      $to = $('#to');
      $ext = $('#ext');
      $btnSearch = $('#btnSearch');
      $sort = $('#sort');
      $frmSearch = $('#frmSearch');

      // $from.datepicker({
      //   dateFormat: "yy-mm-dd"
      // }).datepicker("setDate", "0");
      $from.datepicker({
        dateFormat: "yy-mm-dd"
      });      
      
      $to.datepicker({
        dateFormat: "yy-mm-dd"
      });

      $btnSearch.click(function(){
        let valid = true;

        valid = valid && checkNull($from, "from date");
        valid = valid && checkNull($to, 'to date');
        //valid = valid && checkNull($ext, 'extension or cellphone');

        if(!valid) {
          return false;
        }
      });


    })
  </script>
@endsection