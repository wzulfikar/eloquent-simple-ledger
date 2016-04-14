<div class="panel panel-info">
  <div class="panel-heading">Account Summary</div>
  <div class="panel-body" id="panel-account-summary">
    <div id="chart-account-summary" style="height: 175px;"></div>
    <div class="text-center">Current Balance: <span id="current-balance">{{ $account->balance/100 }}</span></div>
  </div>
</div>

@push('script')
<script type="text/javascript">
// chart account summary
var chartAccountSummary = Morris.Bar({
  element: 'chart-account-summary',
  data: [
    {month:'No data', debit:0, credit:0, balance:0}
  ],
  xkey: 'month',
  ykeys: ['debit', 'credit', 'balance'],
  labels: ['Debit', 'Credit', 'Balance'],
  hideHover:'auto',
});
</script>
@endpush