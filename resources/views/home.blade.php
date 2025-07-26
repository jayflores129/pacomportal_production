@extends('layouts.app')

@section('content')

  <div class="dashboard">
    <div class="task-section">
      @include('home.files', ['files', $files])
    </div>
    <div class="task-section">
      @include('home.tasks', ['tasks', $tasks])
    </div>
    <div class="ticket-section">
      @include('home.tickets', ['tickets', $tickets])
    </div>
     @include('home.tickets-bar')
  </div>

@endsection

@section('css')
<style>
  .table-responsive {
      min-height: 350px;
      max-height: 500px;
      overflow-y: auto;
  }
  div#product_chart2 {
    width: 100%;
    overflow: hidden;
  }
   .btn-default {
    background: #f5f5f5;
    padding: 5px;
    line-height: 1;
    border-radius: 4px;
  }
  .btn-open {
      background: #fb9210;
      color: #000;
  }
  .btn-ps {
    background: #004181;
    color: #fff;
  }
  .btn-cs {
      background: #2ba01c;
      color: #fff;
  }
  .btn-r {
      background: #dc3030;
      color: #fff;
  }
  .btn-rp {
      background: #ebef1a;
      color: #000;
  }
  .btn-rt {
      background: #ae68d2;
      color: #fff;
  } 
  #fileSection .panel {
      min-height: auto !important;
  }
  #fileSection .table-responsive {
    min-height: auto;
    max-height: auto;
  }
  .top-downloads .grid > div {
      padding: 10px 10px;
  }
  .top-downloads .grid {
     border-bottom: 1px solid #f7f6f6;
  }
  .top-downloads .heading-grid {
      background: #f5f5f5;
      font-weight: 600;
  }
  .top-downloads .grid > div:nth-child(1) {
      width: calc(100% - 100px);
  }
  .top-downloads .grid > div:nth-child(2) {
      width: 100px;
      text-align: center;
  }
  #fileSection .panel-body {
      min-height: 300px;
  }
  .panel .panel-body {
      overflow: hidden;
  }
  @media screen and ( max-width: 580px ) {
    .panel.panel-default.panel-brand {
      min-height: auto;
    }
  }
</style>
@endsection

@section('js')

  <!--Load the AJAX API-->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">

    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages':['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawChart);
    google.charts.setOnLoadCallback(barChart);

    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    function drawChart() {

      // Create the data table.
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'product_name');
      data.addColumn('number', 'total');


      var items = <?php echo $topTasks; ?>;

      $.each(items, function(key, value){

             data.addRow([key, value]);
      })
      
      // Set chart options
      var options = {'title':'',
                     'width':550,
                     'height':350};

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.PieChart(document.getElementById('topTasks'));
      chart.draw(data, options);
    }


    function barChart() {


      var items = <?php echo $open_products; ?>;
      var products = [];
      var temp_array = [];
      var colors_array = ['#fb9210', '#004181', '#2ba01c', '#dc3030', '#ae68d2' ];
      var x = 0;

      products.push(['Product', 'Total', { role: 'style' }]);


      $.each(items, function(key, value){
                
                temp_array = [key, value];

                products.push([key, value, colors_array[x]]);

                x++;
      })
      
       var data = google.visualization.arrayToDataTable(products);

       // Set chart options
      var options = { 'title':'',
                      'width':550,
                      'height':350,
                      'bar': {'groupWidth': "75%"},
                      'legend': { 'position': "none" },};

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.BarChart(document.getElementById('product_chart2'));
      chart.draw(data, options);
    }
  </script>
@stop