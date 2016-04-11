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
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.20/pdfmake.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.20/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.1.2/js/buttons.html5.min.js"></script>

<!-- morris chart -->
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

<script type="text/javascript">
function diffHuman(time){
  return moment.duration(moment(time).diff()).humanize(true);
}

function updateLatestTransactionTime(){
  var $row = $('a#latest-transaction'),
      msg  = 'This is your latest transaction, about ' + diffHuman($row.data('created-at'));

  $row.tooltip('hide')
      .attr('data-original-title', msg)
      .tooltip('fixTitle')
      .tooltip('show');
}

function setCurrentBalance(bal){
  $('#current-balance').css('color', bal < 0 ? 'red' : 'black');    
  $('#current-balance').text( bal/100 );
}

function reloadLedgerData(){
  $.getJSON('{{\Request::url()}}', function(data){
    initDt($('#table-ledger'), data.ledger);
    chartAccountSummary.setData(data.summary);
  });
}

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

// chart account summary
var chartAccountSummary = Morris.Bar({
  element: 'chart-account-summary',
  data: [],
  xkey: 'month',
  ykeys: ['debit', 'credit', 'balance'],
  labels: ['Debit', 'Credit', 'Balance'],
  hideHover:'auto',
});

// form handler
$('form[data-ajax="true"] [type="submit"]').click(function(e){
  e.preventDefault();

  var $form = $(this).closest('form');

  $.ajax({
    method:$form.attr('method'),
    url:$form.attr('action'),
    data:$form.serialize(),
    success:function(data){
      if(data.error){
        alert(data.msg);
        return;
      }
      $form.find('[name="amount"], [name="desc"]').each(function(key){
        $(this).val('');
      });

      reloadLedgerData();
    },
    error:function(data){
      console.log('failed to submit ajax form');
    }
  });

});

$('[data-action="refresh-table-ledger"]').click(function(){
  reloadLedgerData();
});


$('#table-ledger').on('draw.dt', function(){
  $(this).find('[data-toggle="tooltip"]').tooltip();
});

reloadLedgerData();
updateLatestTransactionTime();
setInterval(60000, updateLatestTransactionTime);
</script>