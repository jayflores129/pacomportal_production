<div class="ticket-status">  
     <div class="row">
       
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
               <div class="panel-boxes bg-color-1">
                  <div class="panel-heading">
                    <span class="fa fa-ticket"></span>
                  </div>
                  <div class="panel-body">
                       <h5>{{ $open_repairs }}</h5>
                       <span>Open Tickets</span>
                  </div>
              </div>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
               <div class="panel-boxes bg-color-2">
                  <div class="panel-heading">
                    <span class="fa fa-plug"></span>
                  </div>
                  <div class="panel-body">
                       <h5>{{ $repaired_products }}</h5>
                       <span>Received</span>
                  </div>
              </div>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
               <div class="panel-boxes bg-color-3">
                  <div class="panel-heading">
                    <span class="fa fa-wrench"></span>
                  </div>
                  <div class="panel-body">
                       <h5>{{ $repaired_products }}</h5>
                       <span>Repaired</span>
                  </div>
              </div>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
               <div class="panel-boxes bg-color-4">
                  <div class="panel-heading">
                    <span class="fa fa-ship"></span>
                  </div>
                  <div class="panel-body">
                       <h5>{{ $partially_shipped }}</h5>
                       <span>Partially Shipped</span>
                  </div>
              </div>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
               <div class="panel-boxes bg-color-5">
                  <div class="panel-heading">
                    <span class="fa fa-truck"></span>
                  </div>
                  <div class="panel-body">
                       <h5>{{ $completely_shipped }}</h5>
                       <span>Completely Shipped</span>
                  </div>
              </div>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
               <div class="panel-boxes bg-color-6">
                  <div class="panel-heading">
                    <span class="fa fa-exchange"></span>
                  </div>
                  <div class="panel-body">
                       <h5>{{ $returned_products }}</h5>
                       <span>Returned</span>
                  </div>
              </div>
        </div>

     </div>
</div><!-- ./ Ticket Status -->