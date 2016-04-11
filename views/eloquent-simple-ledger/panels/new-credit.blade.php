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