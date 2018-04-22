@extends('layouts.appChuong')

@section('header')
  <link href="{{ asset('DataTables/datatables.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
  <br>

  <p class="text-secondary"><span class="text-danger">Welcome to <i class="font-weight-bold">{{ Auth::user()->name }}</i></span> ( Monitor extension: {{ Auth::user()->roles }} )

  {!! Form::open(['id' => 'frmReport']) !!}
    <div class="form-inline">
      {{ Form::label('from', 'From:', ['class' => 'mb-2 mr-sm-2'])}}
      {{ Form::text('from', Request::input('from') ? Request::input('from') : date('Y-m-d'), ['class' => 'form-control mb-2 mr-sm-2', 'id' => 'from']) }}

      {{ Form::label('to', 'To:', ['class' => 'mb-2 mr-sm-2'])}}
      {{ Form::text('to', Request::input('to') ? Request::input('to') : date('Y-m-d'), ['class' => 'form-control mb-2 mr-sm-2', 'id' => 'to']) }}

      {{-- {{ Form::text('ext', Request::input('ext') ? Request::input('ext') : '',['class' => 'form-control mb-2 mr-sm-2', 'placeholder' => 'extension or cellphone', 'id' => 'ext']) }} --}}

      {{ Form::submit('Submit', ['class' => 'btn btn-primary mb-2 mr-sm-2', 'id' => 'btnReport']) }}
      

      {{ Form::hidden('date', date("Ymd-his")) }}
    </div>
    <div class="row">
      <div class="col-6">
        @if(!empty($exts))      
          @php $i = 0; @endphp
          <div class="row">
            @foreach($exts as $ext) 
              @php $i++; @endphp
                <div class="col-4">                
                  <label class="form-check-label">
                    {{-- <input class="form-check-input" type="checkbox" value=""> Agent {{$ext}} --}}
                    {{ Form::checkbox('exts[]', $ext, true) }} Agent {{$ext}}
                  </label>
                </div>            
              
              @if($i == 3) 
                </div><div class="row">
                @php $i = 0; @endphp
              @endif
              
            @endforeach
          </div>
        @endif
      </div>
    </div>

  {!! Form::close() !!}
  <br>

  <table id="tblReport" class="table"></table>
  <span id="test"></span>
@endsection

@section('scripts')
  {{--  <script src="{{ asset('js/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>  --}}
  <script src="{{ asset('DataTables/datatables.min.js') }}"></script>
  <script src="{{ asset('DataTables/Buttons-1.5.1/js/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('DataTables/Buttons-1.5.1/js/buttons.flash.min.js') }}"></script>
  <script src="{{ asset('DataTables/Buttons-1.5.1/js/buttons.html5.min.js') }}"></script>
  <script src="{{ asset('DataTables/Buttons-1.5.1/js/buttons.print.min.js') }}"></script>
  <script src="{{ asset('DataTables/JSZip-2.5.0/jszip.min.js') }}"></script>

  <script>
    $(document).ready(function(){
      $from       = jQuery('#from');
      $to         = $('#to');
      $btnReport  = jQuery('#btnReport');
      $frmReport  = jQuery('#frmReport');
      $tblReport  = jQuery('#tblReport');

      // datepicker
      $from.datepicker({
        dateFormat: "yy-mm-dd"
      });      
      
      $to.datepicker({
        dateFormat: "yy-mm-dd"
      });

      $btnReport.on('click', function(e) {
        e.preventDefault();
        //$tblReport.empty();
        // bước này để lấy token hiện tại sau đó add vào dataSubmit gửi lên server
        $token = "&_token={{csrf_token()}}";
        //console.log($token);
        $dataSubmit = $frmReport.serialize().concat($token);
        //console.log($dataSubmit);     // Test

        jQuery.ajax({
          url: "{{ url('/report_ob_post') }}",
          method: 'post',
          data: $dataSubmit,
          success: function(results){
            console.log(results);     // test
            //console.log(results.query);
            var jsonString = JSON.stringify(results.query)
            //console.log(jsonString);

            $tblReport.DataTable( {
              destroy: true,
              searching: true,
              paging: false,
              dom: 'Bfrtip',
              buttons: [
                  'copyHtml5',
                  'excelHtml5',
                  'csvHtml5'
              ],
              data: results.query,
              /*columns: [
                  { title: "Exten" },
                  { title: "Total_Dial_Call" },
                  { title: "Total_Time" },
                  { title: "Avg_Time_Call" },
                  { title: "Total_Connect_Call" },
                  { title: "Total_Wait_Time" },
                  { title: "Total_Talk_Time" },
                  { title: "Avg_ConnectTime_Call" }
              ]*/
              "columns" : [
                { "title": "Exten", "data" : "Exten" },
                { "title": "Total Dial Call", "data" : "Total_Dial_Call" },
                { "title": "Total Time", "data" : "Total_Time" },
                { "title": "Avg Time Call", "data" : "Avg_Time_Call" },
                { "title": "Total Connect Call", "data" : "Total_Connect_Call" },
                { "title": "Total Wait Time", "data" : "Total_Wait_Time" },
                { "title": "Total Talk Time", "data" : "Total_Talk_Time" },
                { "title": "Avg ConnectTime Call", "data" : "Avg_ConnectTime_Call" }
              ]              
              } );
          }
        }); // end Ajax
      });
    });
  </script>
@endsection

