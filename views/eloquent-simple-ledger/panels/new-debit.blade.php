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