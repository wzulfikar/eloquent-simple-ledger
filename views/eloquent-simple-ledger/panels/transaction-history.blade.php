<div class="panel panel-success">
  <div class="panel-heading">Transaction History (Current Month)</div>
  <div class="panel-body">
    <div id="chart-transaction-history" style="height: 175px;"></div>
    <!-- <div class="text-center"><span id="count-transactions"></span></span></div> -->
  </div>
</div>

@push('script')
<script type="text/javascript">
// chart transaction history
var chartTransactionHistory = Morris.Line({
  element: 'chart-transaction-history',
  data: [
    {date:'No data', debit:0, credit:0, balance:0}
  ],
  xkey: 'date',
  ykeys: ['debit', 'credit', 'balance'],
  labels: ['Debit', 'Credit', 'Balance'],
  hideHover:'auto',
});
</script>
@endpush