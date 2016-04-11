<div class="panel panel-success">
  <div class="panel-heading">Transaction History (Current Month)</div>
  <div class="panel-body">
    <div id="chart-transaction-history" style="height: 175px;"></div>
    <div class="text-center"><span id="count-transactions"></span></span></div>
  </div>
</div>

@push('script')
<script type="text/javascript">
var data = [
  { date: '2016-04-01', debit: 5, credit: 20, balance: -15, },
  { date: '2016-04-01', debit: 15, credit: 10, balance: 5 },
  { date: '2016-04-02', debit: 10, credit: 5, balance: 10 },
  { date: '2016-04-03', debit: 5, credit: 15, balance: 0 },
  { date: '2016-04-04', debit: 0, credit: 20, balance: -20 },
  { date: '2016-04-04', debit: 0, credit: 20, balance: -20 },
];

// chart transaction history
var chartTransactionHistory = Morris.Line({
  element: 'chart-transaction-history',
  data: data,
  xkey: 'date',
  ykeys: ['debit', 'credit', 'balance'],
  labels: ['Debit', 'Credit', 'Balance'],
  hideHover:'auto',
});

$('#count-transactions').text(!data.length ? 'No Transaction' 
																					 : data.length == 1 ? '1 Transaction' 
																					 										: data.length + ' Transactions');
</script>
@endpush