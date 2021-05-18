<?php
define( "FROM_INDEX", true );

include_once( "init.php" );
?>

<html><head>

<title>Power/Sample Size Calculator</title>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script language="JavaScript" src="js/formula.js"></script>
<script language="JavaScript" src="js/actions.js"></script>
<script language="JavaScript" src="js/jquery.slider.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datejs/1.0/date.min.js"></script>

<script>
    var path = {
        base_url: '<?php echo BASE_URL ?>',       
        relative_url: '<?php echo RELATIVE_URL ?>',
    };
</script>

<link rel="stylesheet" href="styles/bootstrap.min.css" type="text/css" />

<link rel="stylesheet" href="styles/custom.css" type="text/css" />


</head>
<body>
<div class="Container">
        <div class="Header">            
            <div><h1>AB MASTER TOOL</h1></div>
                <div class="actions">
                    View: 
                    <select name="status" id="status" > 
                        <option value="0">All</option>
                        <option value="1">Active</option>
                        <option value="2">Archived</option>
                    </select>  
                    <button id="show-create-exp">Create Experiment</button>
                    <button id="show-add-modal">Add Test Info</button>                 
                </div>
        </div>
        <div class="Content" id="panelContainer">
            <div class="Wrapper">
<div class="col-lg-6 LeftContent panel one">
<h3>AB TESTS</h3>    
<?php 
foreach($tests as $test){?>
<div class="col-md-12 show-details">
    <h4><?php echo $test['name'];?></h4>
    <div class="section">
        <div class="col-md-12 topic"> 
        <div class="col-md-4">    
        <span>Experiment Start Date</span><div class="start-date"><?php echo $test["created_on"]?></div>
        </div>
        <div class="col-md-8 sample-size">    
            <span> Sample Size - 
            <?php if($test["sample_size"] != null){
                echo $test["sample_size"];
            } else{?>
               <form method="post">
               <input type="text" value="<?php echo $test['id']?>" name="id" style="display: none">    
               <input type="text" placeholder="" class="" name="sample_size" ><button class="sample_size_btn">Submit</button></form>
                <?php
            }
            ?>
    </span></div></div>
   
    <br/><label>Experiment Results</label>        
                <?php 
                foreach($test_var as $experiment_data){                
                    if($experiment_data["experiment"]["experiment_name"] == $test['name'] ){                       
                        $total_instances = array_sum( array_column( $experiment_data["variations"], "instance_count" ) );
                        $categorized = ! ( count( $experiment_data["categories"] ) == 1 && isset( $experiment_data["categories"]["uncategorized"] ) );
                        $categories = &$experiment_data["categories"];
                        asort( $categories );
                                           
                    ?>
                    <table>
                <thead>
                <tr>
                    <th rowspan="<?php echo $categorized ? 3 : 2 ?>">Variations</th>
                    <th rowspan="<?php echo $categorized ? 3 : 2 ?>">Distribution</th>
                    <th rowspan="<?php echo $categorized ? 3 : 2 ?>">Visitors</th>
                    <th colspan="<?php echo count( $experiment_data["variations"][0]["events"] ); ?>">Events</th>
                </tr>
                <?php

                    if ( $categorized ) echo "<tr>";

                    $events = [];
                    foreach ( $categories as $category_key => $category_data ) {

                        if ( $categorized ) echo "<th class=\"mh\" colspan=\"" . count( $category_data["events"] ) . "\">{$category_data["caption"]}<br /><small>({$category_key})</small></th>";
                        $events[ $category_key ] = $category_data["events"];
                        ksort( $events[ $category_key ] );

                    }

                    if ( $categorized ) echo "</tr>";

                ?>
                <tr>
                <?php

                    foreach ( $events as $event ) {

                        foreach ( $event as $event_key => $event_caption ) {

                            echo "<th class=\"mh\">{$event_caption}<br /><small>({$event_key})</small></th>";

                        }

                    }

                ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $experiment_data["variations"] as $variation ) { ?>
                    <tr>
                        <td><?php echo "{$variation["variation_caption"]}<br /><small>({$variation["variation_key"]})</small>"; ?></td>
                        <td><?php echo $variation["traffic_distribution"]; ?>%</td>
                        <td><?php printf( "%s<br /><small>%.2f%%</small>", $variation["instance_count"], $total_instances ? ( $variation["instance_count"] / $total_instances ) * 100 : 0 ); ?></td>
                        <?php

                            foreach ( $events as $category_key => $event ) {

                                foreach ( $event as $event_key => $event_caption ) {

                                    $trigger_count = ! empty( $variation["events"]["{$category_key}_{$event_key}"] ) ? $variation["events"]["{$category_key}_{$event_key}"] : 0;
                                    echo "<td class=\"mh\">{$trigger_count}<br /><small>" . ( ( $variation["instance_count"] && $trigger_count ) ? sprintf( "%.2f", ( $trigger_count / $variation["instance_count"] ) * 100 ) : "0.00" ) . "%</small></td>";

                                }

                            }

                        ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="modal ab_modal traffic_modal">
            <div class="modal-content">
            <div class="modal-header">          
                <h4 class="modal-title">Change Traffic Distribution</h4>
            </div>
            <div class="modal-body">           
                
                <form method="post"> 
                    <?php foreach ( $experiment_data["variations"] as $variation ) { ?> 
                        <label for="fname"><?php echo $variation["variation_caption"]?></label><br>
                        <input class="variation" type="text" name="<?php echo $variation["variation_caption"]?>"><br>
                    <?php } ?>
                    <div class="col-sm-12 btns">
                    <button class="submit-btn btn-traffic-distribution">Submit</button>
                    <button class="close-btn">Cancel</button>
                    <div style="display: none">
                        <input type="text" value="<?php echo  $test['exp_id']?>"  name="exp_id">
                    </div> 
                    </div>
                    </form>      
                </div>
            </div>
            </div>       
       
                <?php }}?>
            
        
        <div class="tbl-trans">
        <br/><label> Previous Day Payments Summary</label>  
        <table>
        <thead>
                <tr>
                    <th rowspan="2">Date</th>
                    <th colspan="2">BrainTree</th>
                    <th colspan="2">NMI</th>
                    <th colspan="2">Paypal</th>
                </tr>      
                <tr>
                    <th>Credit</th>
                    <th>Total</th>
                    <th>Credit</th>
                    <th>Total</th>
                    <th>Credit</th>
                    <th>Total</th>                    
                </tr>                      
            </thead>
            <tbody>
                <tr>
                    <td class="td_date"></td>
                    <td class="bt_credit"></td>
                    <td class="bt_total"></td>
                    <td class="nmi_credit"></td>
                    <td class="nmi_total"></td>
                    <td class="paypal_credit"></td>
                    <td class="paypal_total"></td>
                </tr>
            </tbody>                   
        </table>
        
    </div>
    <div>
    <br/><label>Test Information</label>
        <table>
            <tr>
                <th>Element</th>
                <td><?php echo $test['element']?></td>
                <th>Date Started</th>
                <td><?php echo  $test['start_date']?></td>
                <th>Objective</th>
                <td><?php echo $test['objective']?></td>
            </tr>
            <tr>
                <th>Description</th>
                <td colspan="5"><?php echo  $test['description']?></td>
            </tr>
            <tr>
                <th>Justification</th>
                <td><?php echo $test['justification']?></td>
                <th>JIRA Ticket</th>
                <td><a target="_blank" href="<?php echo  "https://socialcatfish.atlassian.net/browse/".$test['jira_id']?>">Go to Ticket</a></td>
                <th>Traffic Allocation</th>
                <td><?php echo $test['traffic_allocation']?></td>
            </tr>
            <tr>
                <th>Force Link</th>
                <td colspan="5"><a href="<?php echo $test['force_link']?>"><?php echo $test['force_link']?></a></td>
            </tr>
            <tr>
                <th>Gut Check 3</th>
                <td><?php echo $test['gut_check_3']?></td>
                <th>Gut Check 14</th>
                <td><?php echo $test['gut_check_14']?></td>
                <th>Details</th>
                <td><?php echo $test['details']?></td>
            </tr>
        </table>
    </div>
    <div>
        <br/><label> Gut check Fields</label><br/>
        <form method="post">
            <label>3 Days :</label> <input type="text" placeholder="" class="" name="GC3D" >        
            <label>14 Days: </label>  <input type="text" placeholder="" class="" name="GC14D" >
            <input type="text" value="<?php echo $test['id']?>" name="id" style="display: none">
            <button class="btn-GC">Submit</button>
        </form>
    </div>   
    <div class="tbl-adj">
         <?php foreach($adjustments as $adjustment){
                if($adjustment["experiment_id"] == $test['experiment_id'] ){
            ?> 
        <br/><label> Adjustments For this Experiment</label>  
        <?php
        break; 
        }        
        }?>
        <?php foreach($adjustments as $adjustment){
                    if($adjustment["experiment_id"] == $test['experiment_id'] ){
                    ?>
        <h5><?php echo $adjustment["date"]?></h5>
        <table class="tbl-adj-conv">      
            <tbody>            
            <tr>
                   
                    <th colspan="1">Adjustment</th>
                    <td class=""><?php echo $adjustment["adjustment"]?></td>                 
                           
                </tr>       
                <tr>
                    <th colspan="1">Jira ID</th>            
                    <td class=""><?php echo $adjustment["jira_id"]?></td>                 
                                           
                </tr>
                <tr>
                <th colspan="1">Traffic Allocation</th>          
                    <td class=""><?php echo $adjustment["traffic_allocation"]?></td>
                </tr>
                <tr>
                    <th colspan="1">Other Details</th>
                    <td class=""><?php echo $adjustment["other_details"]?></td>
                </tr>
                <tr>
                    <th>Conversion before adjustment</th>
                    <td colspan="4">  
            <table class="tbl-conv" >
            <thead>
                <tr>
                    <th rowspan="<?php echo $categorized ? 3 : 2 ?>">Variations</th>
                    <th rowspan="<?php echo $categorized ? 3 : 2 ?>">Distribution</th>
                    <th rowspan="<?php echo $categorized ? 3 : 2 ?>">Visitors</th>
                    <th colspan="<?php echo count( $experiment_data["variations"][0]["events"] ); ?>">Events</th>
                </tr>
                <?php

                    if ( $categorized ) echo "<tr>";

                    $events = [];
                    foreach ( $categories as $category_key => $category_data ) {

                        if ( $categorized ) echo "<th class=\"mh\" colspan=\"" . count( $category_data["events"] ) . "\">{$category_data["caption"]}<br /><small>({$category_key})</small></th>";
                        $events[ $category_key ] = $category_data["events"];
                        ksort( $events[ $category_key ] );

                    }

                    if ( $categorized ) echo "</tr>";

                ?>
                <tr>
                <?php

                    foreach ( $events as $event ) {

                        foreach ( $event as $event_key => $event_caption ) {

                            echo "<th class=\"mh\">{$event_caption}<br /><small>({$event_key})</small></th>";

                        }

                    }

                ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $experiment_data["variations"] as $variation ) { ?>
                    <tr>
                        <td><?php echo "{$variation["variation_caption"]}<br /><small>({$variation["variation_key"]})</small>"; ?></td>
                        <td><?php echo $variation["traffic_distribution"]; ?>%</td>
                        <td><?php printf( "%s<br /><small>%.2f%%</small>", $variation["instance_count"], $total_instances ? ( $variation["instance_count"] / $total_instances ) * 100 : 0 ); ?></td>
                        <?php

                            foreach ( $events as $category_key => $event ) {

                                foreach ( $event as $event_key => $event_caption ) {

                                    $trigger_count = ! empty( $variation["events"]["{$category_key}_{$event_key}"] ) ? $variation["events"]["{$category_key}_{$event_key}"] : 0;
                                    echo "<td class=\"mh\">{$trigger_count}<br /><small>" . ( ( $variation["instance_count"] && $trigger_count ) ? sprintf( "%.2f", ( $trigger_count / $variation["instance_count"] ) * 100 ) : "0.00" ) . "%</small></td>";

                                }

                            }

                        ?>
                    </tr>
                <?php } ?>
            </tbody>   
        
        </table></td></tr>
               
                
            </tbody>                   
        </table>
        <?php } 
                } ?>
    </div>
     <button class="adjustments" id="<?php echo $test['experiment_id'];?>"> Make Adjustment</button>
     <button class="btn-traffic"> Change Traffic </button>  
     <div class="inline">
        <form method="post">            
           <button class="btn-restart" id="<?php echo  $test['exp_id']?>">Restart</button>
        </form>        
     </div>
    <?php if( $test['status'] != "2"){?>
        <div class="inline">
        <form method="post">
            <input type="text" value="<?php echo $test['id']?>" name="id" style="display: none">
            <button class="btn-archive">Archive</button>
        </form>
        
        </div>
    <?php } ?>
    </div>

</div>
<?php } ?>
</div>
<div class="col-lg-6 RightContent panel two">  
<div class="col-md-12">
        <h3> Daily Conversion Data</h3>
        <div>
        <label>Experiment</label> <select id="daily_conversion_exp_id" class="input-name" name="experiment_id" data-default="Select an Experiment ID">
                        <option value="">Select an Experiment</option>
                        <?php
                            $experiments  = AB_Tests::get_experiment_ids();
                            foreach($experiments as $exp){                              
                                    echo "<option value='".$exp['id']."'>".$exp['name']."</option>";                                
                            }
                        ?>
                    </select>
                </div>
        <div id="conversions-date-range-selector"></div>
        <div id="conversions"></div>
        <div id="conversions_diff"></div>  
        <hr>
    </div>    
                
    <div class="col-md-12">
        <h3> Google Analytics Data</h3>
        <div id="embed-api-auth-container"></div>       
        <div id="view-selector-container"></div>  
        <div id="date-range-selector-1-container"></div>
        <div class="metric">
        <label for="metrics">Choose a Metric</label>
        <select name="metrics" id="metrics" > 
            <option value="ga:sessions">Sessions</option>
            <option value="ga:users">Users</option>
            <option value="ga:newUsers">New Users</option>
            <option value="ga:transactionsPerSession">Conversions</option>        
        </select>
        </div>
        <div id="timeline"></div>  
        <hr>
    </div>   

    <div class="col-md-12">
    <form>
        <h3>Sample Size Formula</h3>
        <table>
        <tbody><tr>
        <th align="LEFT">Enter a value for p1:
        </th><td><input type="TEXT" name="p1" size="10">
        </td></tr><tr>
        <th align="LEFT">Enter a value for p2:
        </th><td><input type="TEXT" name="p2" size="10">
        </td></tr></tbody></table>
        <table>
        <tbody><tr>
        <th align="LEFT">Enter a value for α (default is .05):
        </th><td><input type="TEXT" name="alpha" size="10" value=".05">
        </td></tr><tr>
        <th align="LEFT">Enter a value for desired power (default is .80):
        </th><td><input type="TEXT" name="power" size="10" value=".80">
        </td></tr><tr>
        <th align="LEFT">The sample size (for each sample separately) is:
        </th><td>
        <input type="TEXT" name="ss" size="10">
        </td></tr></tbody></table>
        <br>
        <input type="BUTTON" value="Calculate" onclick="b2(this.form)">
        </form>
        <hr>
    </div> 
    <span class="slider"></span>

 </div>
 </div>
    </div>
<?php 
    include("modals/add_info.php"); 
    include("modals/add_adjustment.php"); 
    include("modals/restart_confirmation.php"); 
    include("modals/create_experiment.php");
 ?>
</body>
<script>
        (function(w,d,s,g,js,fs){
          g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
          js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
          js.src='https://apis.google.com/js/platform.js';
          fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
        }(window,document,'script'));
        
</script>
<script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/components/view-selector2.js"></script>
<script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/components/date-range-selector.js"></script>
<script language="JavaScript" src="js/g-analytics.js"></script>

</html>