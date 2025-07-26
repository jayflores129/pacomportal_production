@if( Auth::user()->isAdmin() )
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Status</label>
    <div class="col-sm-9">
        <ul class="list-inline">
          <li>
              <div class="radio">
                <label>
                  <input type="radio" name="status"  value="Under Reviewed" {{ ( $repair->status === 'Under Reviewed' )  ? 'checked' : '' }}>
                  <strong>Under Reviewed</strong>
              </label>
            </div>
          </li>
          <li>
            <div class="radio">
              <label>
                <input type="radio" name="status"  value="To Be Confirmed" {{ ( $repair->status === 'To Be Confirmed' )  ? 'checked' : ''}}>
                  <strong>To Be Confirmed</strong>
              </label>
            </div>
        </li>
        <li>
          <div class="radio">
            <label>
              <input type="radio" name="status"  value="Confirmed" {{ ( $repair->status == 'Confirmed' )  ? 'checked' : ''}}>
                <strong>Confirmed</strong>
            </label>
          </div>
      </li>
          <li>
              <div class="radio">
                <label>
                  <input type="radio" name="status"  value="Received" {{ ( $repair->status === 'Received' )  ? 'checked' : ''}}>
                    <strong>Received</strong>
                </label>
              </div>
          </li>
            <li>
              <div class="radio">
                <label>
                  <input type="radio" name="status"  value="Completed" {{ ( $repair->status === 'Completed' )  ? 'checked' : ''}}>
                  <strong>Completed</strong>
                </label>
              </div>   
          </li>
          <li>
              <div class="radio">
                <label>
                  <input type="radio" name="status"  value="Shipped" {{ ( $repair->status == 'Shipped' )  ? 'checked' : ''}}>
                    <strong>Shipped</strong>
                </label>
              </div>
          </li>
            <li>
              <div class="radio">
                <label>
                  <input type="radio" name="status"  value="Cancelled" {{ ( $repair->status == 'Cancelled' )  ? 'checked' : ''}}>
                  <strong>Cancelled</strong>
                </label>
              </div>
          </li>
      </ul>
    </div>
</div> 
@else
<input type="hidden" name="status"  value="{{$repair->status }}">

@endif