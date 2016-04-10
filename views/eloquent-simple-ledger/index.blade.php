@extends('eloquent-simple-ledger.layout')

@push('head')
<link href="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.11/css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/buttons/1.1.2/css/buttons.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.0.2/css/responsive.dataTables.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-default">
        <div class="panel-heading">
        Ledger of Account #{{$account_id}}
        
        <a class="pull-right" title="Refresh ledger" data-toggle="tooltip" data-action="refresh-table-ledger"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></a>

        </div>

        <div class="panel-body">
          <table id="table-ledger" class="table table-bordered table-striped table-hover display responsive no-wrap" width="100%">
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

@endsection

@push('script')
<!-- helpers -->
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.12.0/moment.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.11/js/jquery.dataTables.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.11/js/dataTables.bootstrap.min.js"></script>

<!-- datatables responsive extension -->
<script src="//cdn.datatables.net/responsive/2.0.2/js/dataTables.responsive.min.js"></script>

<!-- buttons for datatables -->
<script src="//cdn.datatables.net/buttons/1.1.2/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.1.2/js/buttons.flash.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.1.2/js/buttons.html5.min.js"></script>

<script type="text/javascript">
var last_record = {!! $last_record !!};

function reloadLedgerDt(){
  $.getJSON('{{\Request::url()}}', function(data){
    initDt($('#table-ledger'), data);
  });
}

function initDt($el, data){

  if ( $.fn.dataTable.isDataTable( $el ) ) {
    $el.DataTable().clear().rows.add(data).draw();
    return $el;
  }

  return $el.DataTable({
    responsive: true,
    data:data,
    // order: [[0, 'created_at']],
    aLengthMenu: [[5, 10, 25], [5, 10, 25]],
    iDisplayLength: 10,
    deferRender: true,
    processing: true,
    serverSide: false,
    columns:[
      {
        title:'Time',
        name:'created_at',
        data:'created_at',
        render:function(data, type, row){
          var render = moment(data).format('dddd, DD MMM YYYY [at] HH:mm:ss a');

          if(row.id == last_record.id){
            render += '<a class="pull-right" title="This is your latest transaction, ' 
                   +  moment.duration(row.created_at).humanize(true)
                   + '." data-toggle="tooltip"><span class="glyphicon glyphicon-info-sign"></span></a>';
          }

          return render;
        }
      },
      {
        title:'Description',
        name:'desc',
        data:'desc',
      },   
      {
        title:'Debit',
        name:'debit',
        data:'debit',
        sClass:'text-right',
        render:function(data){
          if(data == null) return null;

          // divide debit by 100 if you want to represent it in cents
          data /= 100;
          return data.toString().indexOf('.') > -1 ? data : data + '.00';
        }
      },
      {
        title:'Credit',
        name:'credit',
        data:'credit',
        sClass:'text-right',
        render:function(data){
          if(data == null) return null;

          // divide credit by 100 if you want to represent it in cents
          data /= 100;
          return data.toString().indexOf('.') > -1 ? data : data + '.00';
        }
      },
      {
        title:'Balance',
        name:'balance',
        data:'balance',
        sClass:'text-right',
        render:function(data, type, row){
          if(data == null) return null;

          data /= 100; // represent balance in cents

          // if balance is zero or less, make it red
          data = data.toString().indexOf('.') > -1 ? data : data + '.00';

          var render = Number.parseFloat(data) < 0 ? '<span style="color:red;">' + data + '</span>' : data;
          
          if(row.id == last_record.id){
            render += '<a class="pull-left" title="Your current balance is '
                   +  data
                   + '" data-toggle="tooltip"><span class="glyphicon glyphicon-info-sign"></span></a>';
          }

          return render;
        }
      }
    ],

    // export functions
    dom: 'Bfrtip',
    buttons: [
      // 'copyHtml5',
      'excelHtml5',
      'csvHtml5',
      'pdfHtml5'
    ]

  })
}

$('[data-action="refresh-table-ledger"]').click(function(){
  reloadLedgerDt();
});

reloadLedgerDt();

$('#table-ledger').on('draw.dt', function(){
  $(this).find('[data-toggle="tooltip"]').tooltip();
});

</script>
@endpush
