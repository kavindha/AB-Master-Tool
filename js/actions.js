(function($) {   
    $(document).ready(function() {   

        var url = new URL(document.location);          
        var params = url.searchParams;
        
        var status = params.get("status");
        if(status -= null){           
            $("#status").val(status);
        } 

        $('.show-details h4').click(function(){                     
            if($(this).parent().children('.section').css('display') == "block"){
                $(this).parent().children('.section').css('display', 'none');                
            }else if($(this).parent().children('.section').css('display') == "none"){                
                $(".payment-details").css('visibility', 'hidden');
                $(this).parent().children('.section').css('display', 'block');
            }
        });
    
        var dates = [];
        $(".start-date").each(function(){
            date =$(this).text();         
            dates.push(date);
        });

        $.ajax({
            url: "http://localhost/scf/_reporting_tool_AB_master.html",
            data: {"dates" : dates},
            dataType: "json",
            crossDomain: true,
            method: "post",
            setRequestHeader:( 'X-Requested-With', 'XMLHttpRequest' ),
            success: function(data) {    
              
              for (var key in  data.daily_details) {
                $(".start-date").each(function(){
                    date =$(this).text();
                    $element = $(this).parent().parent().parent();
                    if(date == key) {
                        $element.find('.td_date').html(data.daily_details[key].date);
                        $element.find('.bt_credit').html(data.daily_details[key].braintree.credit);
                        $element.find('.bt_total').html(data.daily_details[key].braintree.total);
                        $element.find('.nmi_credit').html(data.daily_details[key].nmi.credit);
                        $element.find('.nmi_total').html(data.daily_details[key].nmi.total);
                        $element.find('.paypal_credit').html(data.daily_details[key].paypal.credit);
                        $element.find('.paypal_total').html(data.daily_details[key].paypal.total);
                    }

                });
              }
               
            }
        });
           

        
        $('.btn-archive').click(function(e){
            e.preventDefault();          
            var post_data = $(this).parents("form").eq(0).serialize();  
            $.ajax({
                url: "ajax.php?cmd=archive_test",
                data: post_data,
                dataType: "json",
                crossDomain: true,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "post",
                success: function(data) {               
                    location.reload(); 
                }
            });
        });
        $('#show-add-modal').click(function(){
            $('#add_info_modal').show();
        });
        $('#status').change(function(){
           $status=  $("#status"). children("option:selected"). val();
           window.location.href = path.base_url + "AB-Master.php?status="+$status;
        });

        $('.btn-GC').click(function(e){
            e.preventDefault();        
            var post_data = $(this).parents("form").eq(0).serialize();                  
            $.ajax({
                url: "ajax.php?cmd=gut_check",
                data: post_data,
                dataType: "json",
                crossDomain: true,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "post",
                success: function(data) {               
                    location.reload(); 
                }
            });
        });

        $('.sample_size_btn').click(function(e){
            e.preventDefault();        
            var post_data = $(this).parents("form").eq(0).serialize();                  
            $.ajax({
                url: "ajax.php?cmd=sample_size",
                data: post_data,
                dataType: "json",
                crossDomain: true,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "post",
                success: function(data) {               
                    location.reload(); 
                }
            });
        });

        $('#submit_ab_info').click(function(e){           
            e.preventDefault();
            var post_data = $(this).parents("form").eq(0).serialize();
            $.ajax({
                url:"ajax.php?cmd=add_test_info",
                data: post_data,
                dataType: "json",
                crossDomain: true,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "post",
                success: function(data) {               
                   if(data.status == true){
                        $(".success").css('display','block')
                   } else {
                        $(".fail").css('display','block')
                   }
                }
            })
        });

        $('#ab_info_close').click(function(e){
            e.preventDefault();
            $('#add_info_modal').hide();
        });

        $('.adjustments').click(function(){
            $('#add_adjustment_modal').show();           
            $("#add_adjustment_modal  #exp_id").val($(this).attr('id'));
        });

        $('#submit_adjustment').click(function(e){           
            e.preventDefault();
            var inputs = this.parentElement.parentElement.getElementsByTagName('input'); 
            for(var i = 0; i < inputs.length; i++){
                if(inputs.item(i).value == ""){
                    alert("All fields must be filled");
                    return false;
                }
            } 
            var post_data = $(this).parents("form").eq(0).serialize();
            $.ajax({
                url:"ajax.php?cmd=add_adjustment",
                data: post_data,
                dataType: "json",
                crossDomain: true,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "post",
                success: function(data) {               
                   if(data.status == true){
                        $(".success").css('display','block')
                   } else {
                        $(".fail").css('display','block')
                   }
                }
            })
        });

        $('.btn-restart').click(function(e){
            e.preventDefault();          
            $('#restart_confirmation_modal').show();
            $("#restart_confirmation_modal  #restart_exp_id").val($(this).attr('id'));         
        });

        $('#restart_test').click(function(e){           
            e.preventDefault();
            var post_data = $(this).parents("form").eq(0).serialize();
            $.ajax({
                url:"ajax.php?cmd=restart_test",
                data: post_data,
                dataType: "json",
                crossDomain: true,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "post",
                success: function(data) {               
                    location.reload(); 
                }
            })
        });
        $('#restart_confirmation_modal .close-btn').click(function(e){
            e.preventDefault();         
            $('#restart_confirmation_modal').hide();
        });

        $('#add_adjustment_modal .close-btn').click(function(e){
            e.preventDefault();         
            $('#add_adjustment_modal').hide();
        });

        $('.btn-traffic').click(function(e){
            $(this).parent().find(".traffic_modal").show();
        });

        $('.btn-traffic-distribution').click(function(e){
            e.preventDefault();
            var post_data = $(this).parents("form").eq(0).serialize();
            var val =  this.parentElement.parentElement.getElementsByClassName('variation');            
            var total = 0;
            for (var i = 0; i < val.length; i++) {
                total = total + parseInt(val.item(i).value);
            }           
            if(total != 100){
                alert("Total of traffic distribution should add up to 100");
                return false;
            }
            $.ajax({
                url:"ajax.php?cmd=traffic_distribution",
                data: post_data,
                dataType: "json",
                crossDomain: true,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "post",
                success: function(data) {               
                    
                }
            })
        });
      
        $('.traffic_modal .close-btn').click(function(e){
            $(".traffic_modal").hide();
            e.preventDefault();
        });
        variation_val = 3;
        event_val = 3;
        $('#show-create-exp').click(function(e){
            $("#create_experiment_modal").show();   
            variation_val = 3; 
            event_val = 3;       
        });

        $('#create_experiment_modal .close-btn').click(function(e){
            $("#create_experiment_modal").hide();
            e.preventDefault();
        });

        
        $('#submit_exp').click(function(e){
            e.preventDefault();
            var inputs = this.parentElement.parentElement.getElementsByTagName('input'); 
            for(var i = 0; i < inputs.length; i++){
                if(inputs.item(i).value == ""){
                    alert("All fields must be filled");
                    return false;
                }
            } 

            var post_data = $(this).parents("form").eq(0).serialize();           
            $.ajax({
                url:"ajax.php?cmd=create_experiment",
                data: post_data,
                dataType: "json",
                crossDomain: true,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "post",
                success: function(data) {               
                    $(".new_exp_id").val(data.id);                                 
                    $(".variation_data").show();
                    $(".experiment_data").hide();                   
                }
            })
        });        
        $('#add-btn').click(function(e){
            e.preventDefault();
           $(".variation_data .field").append(' <div class="variation_info"><label>Variation '+variation_val+'</label><div class="input-field col-sm-12"> <input type="text" placeholder="Caption" class="Caption" name="caption" ></div><div class="input-field col-sm-12"><input type="text" placeholder="Key" class="key" name="key">  </div>  <div class="input-field col-sm-12"><input type="text" placeholder="Traffic" class="traffic_distribution" name="traffic_distribution" ></div>');
           variation_val +=1;
        });

        $('#add-event-btn').click(function(e){
            e.preventDefault();
           $(".event_data .field").append(' <div class="event_info"> <label>Event '+variation_val+'</label>  <div class="input-field col-sm-12"> <input type="text" placeholder="Caption" class="caption" name="caption" > </div> <div class="input-field col-sm-12"> <input type="text" placeholder="Key" class="key" name="key"> </div> </div>');
           event_val +=1;
        });

        $('#submit_variations').click(function(e){
            e.preventDefault();
            var inputs = this.parentElement.parentElement.getElementsByTagName('input'); 
            for(var i = 0; i < inputs.length; i++){
                if(inputs.item(i).value == ""){
                    alert("All fields must be filled");
                    return false;
                }
            } 
            var val =  this.parentElement.parentElement.getElementsByClassName('traffic_distribution');            
            var total = 0;
            for (var i = 0; i < val.length; i++) {
                total = total + parseInt(val.item(i).value);
            }           
            if(total != 100){
                alert("Total of traffic distribution should add up to 100");
                return false;   
            }            
            var variations = [];
            $( ".variation_info" ).each(function( index ) {
                variations.push( {
                    caption: $(this).find(".caption").val(),  
                    key: $(this).find(".key").val(),
                    traffic_distribution: $(this).find(".traffic_distribution").val(),            
                } );
              });               
              id =  $(".new_exp_id").val();                          
            $.ajax({
                url:"ajax.php?cmd=create_variations",
                data: { id : id, variations :variations},
                dataType: "json",
                crossDomain: true,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "post",
                success: function(data) {               
                          
                }
            })
            $(".variation_data").hide();
            $(".event_data").show();            
        });

        $('#submit_events').click(function(e){
            e.preventDefault();
            var inputs = this.parentElement.parentElement.getElementsByTagName('input'); 
            for(var i = 0; i < inputs.length; i++){
                if(inputs.item(i).value == ""){
                    alert("All fields must be filled");
                    return false;
                }
            }                        
            var events = [];
            $( ".event_info" ).each(function( index ) {
                events.push( {
                    caption: $(this).find(".caption").val(),  
                    key: $(this).find(".key").val(),                       
                } );
              });               
            id =  $(".new_exp_id").val();                        
            $.ajax({
                url:"ajax.php?cmd=create_events",
                data: { id : id, events :events},
                dataType: "json",
                crossDomain: true,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "post",
                success: function(data) {               
                          
                }
            })
            $(".event_data").hide();
            $(".exp_info_data").show();            
        });

        $('#submit_ab_info_new').click(function(e){           
            e.preventDefault();
            var post_data = $(this).parents("form").eq(0).serialize();
            $.ajax({
                url:"ajax.php?cmd=add_test_info_new",
                data: post_data,
                dataType: "json",
                crossDomain: true,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "post",
                success: function(data) {               
                   if(data.status == true){
                        $(".success").css('display','block')
                        location.reload(); 
                   } else {
                        $(".fail").css('display','block')
                   }
                }
            })
        });
     
        google.charts.load('current', {'packages':['corechart']});
                
        gapi.analytics.ready(function() {
        var dateRange = {
            'start-date': 'today',
            'end-date': 'today'
        };
        var conversions_date_range = new gapi.analytics.ext.DateRangeSelector({
            container: 'conversions-date-range-selector'
            })           
            .set(dateRange)
            .execute()
            
        conversions_date_range.on('change', function(data) {
            dateRange['start-date']= data['start-date'];
            dateRange['end-date']= data['end-date'];    
            if(document.getElementById("daily_conversion_exp_id").value == ""){
                alert("Select an experiment");
                return;
            }
            var post_data ={
                exp_id : document.getElementById("daily_conversion_exp_id").value,
                start_date: dateRange['start-date'],
                end_date : dateRange['end-date'],      
            };
            $.ajax({
                url:"ajax.php?cmd=daily_conversions",               
                data: post_data,
                dataType: "json",
                method: "post",
                success: function(data) {   
                    if(data.status == true){                             
                        drawChart( data.daily_conv);
                        drawChart2( data.daily_conv);
                    }
                 }
            });
        });       
        
        $('#daily_conversion_exp_id').on('change', function(data) {         
             dateRange['start-date']= conversions_date_range["start-date"];
            dateRange['end-date']= conversions_date_range["end-date"];  
            if(document.getElementById("daily_conversion_exp_id").value == ""){
                alert("Select an experiment");
                return;
            }
            var post_data ={
                exp_id : document.getElementById("daily_conversion_exp_id").value,
                start_date: dateRange['start-date'],
                end_date : dateRange['end-date'],      
            };
            $.ajax({
                url:"ajax.php?cmd=daily_conversions",               
                data: post_data,
                dataType: "json",
                method: "post",
                success: function(data) {   
                    if(data.status == true){                             
                        drawChart( data.daily_conv);
                        drawChart2( data.daily_conv);
                    }
                 }
            });
        });       


        function drawChart($daily_conversions){
            var container = document.getElementById("conversions");
            // Create the data table.                        
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Date');
            data.addColumn('number', 'Standard Mobile');
            data.addColumn('number', 'Standard Desktop');
            data.addColumn('number', 'Variant Mobile');
            data.addColumn('number', 'Variant Desktop');
            $.each($daily_conversions, function (i, obj) {
               var formattedDate = new Date(obj.Date);      
               var d = formattedDate.getDate();
               var m =  formattedDate.getMonth();
               m += 1;         
                data.addRows([
                    [d+"-"+m,parseInt(obj.Standard_Mobile),parseInt(obj.Standard_Desktop),parseInt(obj.Variant_Mobile),parseInt(obj.Variant_Desktop)],
                ]);
            });

       
            var options = {'title':'Daily Conversion Total',
            chartArea: {
                height: '100%',
                width: '100%',
                top: 48,
                left: 48,
                right: 16,
                bottom: 48
              },
            'width':'100%',
            'curveType' : 'function',
            legend: { position: 'bottom' },
            'height':400};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById('conversions'));
        chart.draw(data, options);
        }

        function drawChart2($daily_conversions){
            var container = document.getElementById("conversions_diff");
            // Create the data table.                        
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Date');
            data.addColumn('number', 'Mobile');
            data.addColumn('number', 'Desktop'); 
            $.each($daily_conversions, function (i, obj) {
               var formattedDate = new Date(obj.Date);      
               var d = formattedDate.getDate();
               var m =  formattedDate.getMonth();
               m += 1;         
                data.addRows([
                    [d+"-"+m,(parseFloat(obj.Variant_Mobile)-parseFloat(obj.Standard_Mobile))/parseFloat(obj.Standard_Mobile),(parseFloat(obj.Variant_Desktop)-parseFloat(obj.Standard_Desktop))/parseFloat(obj.Standard_Desktop)],
                ]);
            });

       
            var options = {'title':'Daily Conversion Rate Variant vs Standard',
            chartArea: {
                height: '100%',
                width: '100%',
                top: 48,
                left: 48,
                right: 16,
                bottom: 48
              },
            'width':'100%',
            'curveType' : 'function',
            legend: { position: 'bottom' },
            'height':400};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById('conversions_diff'));
        chart.draw(data, options);
        }
    });
    } );     
})(jQuery);