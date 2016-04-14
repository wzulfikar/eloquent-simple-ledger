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
updateLatestTransactionTime();
setInterval(60000, updateLatestTransactionTime);
</script>