@extends('eloquent-simple-ledger.layout')

@push('head')
<link href="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.11/css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/buttons/1.1.2/css/buttons.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.0.2/css/responsive.dataTables.min.css" rel="stylesheet">

<!-- morris -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">

<style type="text/css">
#table-ledger_length{
  float: left;
}
#current-balance {
  font-weight: bold;
}
.btn:focus, .btn:focus:active, .paginate_button a:focus, .paginate_button a:focus:active{
  outline-color:white !important;
}
</style>
@endpush

@section('content')
<div class="container">
  
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="row">

        <div class="col-md-4 col-sm-12">
          <div class="panel panel-info">
            <div class="panel-heading">Account Summary</div>
            <div class="panel-body" id="panel-account-summary">
              <div id="chart-account-summary" style="height: 175px;"></div>
              <div class="text-center">Current Balance: <span id="current-balance">{{ $account->balance/100 }}</span></div>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-12">
          <div class="panel panel-success">
            <div class="panel-heading">New Debit</div>
            <div class="panel-body">

              <form id="form-new-debit" action="{{ \Request::url() }}" method="POST" data-ajax="true">
                {{ csrf_field() }}
                <input type="hidden" name="action" value="debit"></input>
                <div class="form-group">
                  <label>Debit Amount</label>
                  <input name="amount" type="number" class="form-control" placeholder="Insert amount" required autofocus>
                </div>
                <div class="form-group">
                  <label>Debit Description</label>
                  <textarea name="desc" class="form-control" placeholder="Insert description" required></textarea>
                </div>

                <div class="row-fluid pull-right">
                  <button type="reset" class="btn btn-default">Reset</button>
                  <button type="submit" class="btn btn-success">Save</button>
                </div>
                
              </form>

            </div>
          </div>
        </div>        

        <div class="col-md-4 col-sm-12">
          <div class="panel panel-warning">
            <div class="panel-heading">New Credit</div>
            <div class="panel-body">
              
              <form id="form-new-credit" action="{{ \Request::url() }}" method="POST" data-ajax="true">
                {{ csrf_field() }}
                <input type="hidden" name="action" value="credit"></input>
                <div class="form-group">
                  <label>Credit Amount</label>
                  <input name="amount" type="number" class="form-control" placeholder="Insert amount" required>
                </div>
                <div class="form-group">
                  <label>Credit Description</label>
                  <textarea name="desc" class="form-control" placeholder="Insert description" required></textarea>
                </div>
                <div class="row-fluid pull-right">
                  <button type="reset" class="btn btn-default">Reset</button>
                  <button type="submit" class="btn btn-warning">Save</button>
                </div>
              </form>

            </div>
          </div>
        </div>

      </div>

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
    </div>
  </div>
</div>

@endsection

@push('script')
  @include('eloquent-simple-ledger.js.index')
@endpush
