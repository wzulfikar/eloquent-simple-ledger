<div class="panel panel-primary">
  <div class="panel-heading">
  Ledger of Account #{{$account->id}}
  
  <a class="pull-right" title="Refresh ledger" data-toggle="tooltip" data-action="refresh-table-ledger"><span class="glyphicon glyphicon-repeat" aria-hidden="true" style="color:white;"></span></a>

  </div>

  <div class="panel-body">
    <table id="table-ledger" class="table table-bordered table-striped table-hover display responsive no-wrap" width="100%">
    </table>
  </div>

</div>

@push('script')
<script type="text/javascript">

$('[data-action="refresh-table-ledger"]').click(function(){
  reloadLedgerData();
});


$('#table-ledger').on('draw.dt', function(){
  $(this).find('[data-toggle="tooltip"]').tooltip();
});

function initDt($el, data){
  var columnDefs,
      last_record = data[data.length - 1];

  // update current balance
  setCurrentBalance(last_record ? last_record.balance : 0);

  var columnDefs = [
    {
      title:'Time',
      name:'created_at',
      data:'created_at',
      render:function(data, type, row){
        var render = moment(data).format('dddd, DD MMM YYYY [at] HH:mm:ss a');

        if(row.id == last_record.id){
          render += '<a class="pull-right" data-created-at="' 
                 + row.created_at
                 + '" id="latest-transaction"'
                 + '." data-toggle="tooltip" title="This is your latest transaction, about '
                 + diffHuman(row.created_at) 
                 + '"><span class="glyphicon glyphicon-info-sign"></span></a>';
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
  ];

  if ( $.fn.dataTable.isDataTable( $el ) ) {
    $el.DataTable().destroy();
  }

  return $el.DataTable({
    responsive: true,
    data:data,
    // order: [[0, 'created_at']],
    aLengthMenu: [[5, 10, 25], [5, 10, 25]],
    iDisplayLength: 5,
    deferRender: true,
    processing: true,
    serverSide: false,
    columns:columnDefs,

    // export functions
    dom: 'Blfrtip',
    buttons: [
      // 'copyHtml5',
      'excelHtml5',
      'csvHtml5',
      'pdfHtml5',
    ]

  });
}

function reloadLedgerData(){
  $.getJSON('{{\Request::url()}}', function(data){
    initDt($('#table-ledger'), data.ledger);
    
    if(data.summary.length)
      chartAccountSummary.setData(data.summary);

    if(data.transactions.length){
      chartTransactionHistory.setData(data.transactions);
    }
  });
}

reloadLedgerData();
</script>
@endpush