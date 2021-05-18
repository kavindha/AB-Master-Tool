gapi.analytics.ready(function() {

  /**
   * Authorize the user immediately if the user has already granted access.
   * If no access has been created, render an authorize button inside the
   * element with the ID "embed-api-auth-container".
   */
  gapi.analytics.auth.authorize({
    container: 'embed-api-auth-container',
    clientid: '958810300976-ojj0lh5eo67ttusj39d4lpthos0ed8ge.apps.googleusercontent.com'
  });

  gapi.analytics.auth.on('signIn', function() {
            $(".metric, #date-range-selector-1-container").css('display', 'inline-block');
          }); 
  /**
   * Create a new ViewSelector instance to be rendered inside of an
   * element with the id "view-selector-container".
   */
  
  var viewSelector = new gapi.analytics.ext.ViewSelector2({
    container: 'view-selector-container',
  }).execute();  

  var dateRange1 = {
        'start-date': 'today',
        'end-date': 'today'
    };
    
  var dateRange2 = {
        'start-date': 'today',
        'end-date': 'today'
    };

    var dateRangeSelector1 = new gapi.analytics.ext.DateRangeSelector({
    container: 'date-range-selector-1-container'
    })
    .set(dateRange1)
    .execute()

    dateRangeSelector1.on('change', function(data) {
        dateRange1['start-date']= data['start-date'];
        dateRange1['end-date']= data['end-date'];
       
        var startDate = new Date(data['start-date']);
        startDate.setFullYear( startDate.getFullYear() - 1 )
        startDate = new Date(startDate).toString('yyyy-MM-dd');
        var endDate = new Date(data['end-date']);        
        endDate.setFullYear( endDate.getFullYear() - 1 )  
        endDate = new Date(endDate).toString('yyyy-MM-dd')    
        dateRange2['start-date']= startDate;
        dateRange2['end-date']= endDate;     
        createChart();
    });
  /**
   * Render the dataChart on the page whenever a new view is selected.
   */
  viewSelector.on('change', function(ids) {   
    google.load("visualization", "1.1", {packages:["corechart"]});
    google.setOnLoadCallback(createChart);
  });
 
  document.getElementById("metrics").addEventListener("change", createChart);

function createChart (){
  var metric = document.getElementById("metrics").value;
  var data1 = new gapi.analytics.report.Data({
    query: {
        ids: "ga:69952076",
        dimensions: 'ga:date',
        metrics: metric,
        'start-date': dateRange1['start-date'],
        'end-date': dateRange1['end-date'],        
        output: 'dataTable'
    }
});

var data2 = new gapi.analytics.report.Data({
    query: {
        'ids': "ga:69952076",
        'dimensions': 'ga:date',
        'metrics': metric,
        'start-date': dateRange2['start-date'],
        'end-date': dateRange2['end-date'], 
        'output': 'dataTable'
    }
});

data1.on('success', function(response1) {
    var table1 = new google.visualization.DataTable(response1.dataTable);  
    $.each(table1.Vf, function(i, obj){      
        obj.c[0].v = new Date(new Date(obj.c[0].v).toString('MM/dd')); 
    })   
    data2.execute();
    data2.on('success', function(response2) {
        var table2 = new google.visualization.DataTable(response2.dataTable);
        $.each(table2.Vf, function(i, obj){       
            obj.c[0].v = new Date(new Date(obj.c[0].v).toString('MM/dd')); 
        })
        var dataFinal = google.visualization.data.join(table1, table2, 'full', [[0,0]],[1],[1]);
        dataFinal.setColumnLabel(1, "This Year");
        dataFinal.setColumnLabel(2, "Prev Year");
        var options = {                 
          'interpolateNulls': true,
          hAxis: {
            format: 'M/dd'            
          }          
        };
        chart.draw(dataFinal, options);

    });
});
data1.execute();
var chart = new google.visualization.LineChart(document.getElementById('timeline'));
}

});
