<?php
define( "FROM_INDEX", true );

include_once( "init.php" );
?>

<html><head>

<title>Power/Sample Size Calculator</title>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script language="JavaScript" src="formula.js"></script>
<script language="JavaScript" src="actions.js"></script>
<link rel="stylesheet" href="custom.css" type="text/css" />


</head

><body>
<?php 
foreach($tests as $test){?>
<div class="show-details">
<h3><?php echo $test['name'];?></h3>
<div class="section">
    <table style="width: 100%; margin-top: 10px; font-size: 0.8em;" border="1px">
        <thead>
            <tr>
                <th rowspan="3">Variation</th>
                <th rowspan="3">Distribution</th>
                <th  rowspan="3">Visitors</th>
                <th colspan="2">Event</th>
            </tr>
            <tr>
                <th>Desktop</th>
                <th>Mobile</th>
                
            </tr>
            <tr>
                <th>Cenversion</th>
                <th>Conversion</th>
                
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach($test_var as $var){
                if($var["experiment_name"] == $test['name'] ){
                ?>
                <tr>
                    <td><?php echo $var['variation_caption'];?></td>
                    <td><?php echo $var['traffic_distribution'];?></td>
                    <td><?php echo $var['instance_count'];?></td>
                    <td><?php echo $var['variation_caption'];?></td>
                    <td><?php echo $var['variation_caption'];?></td>
                </tr>
            <?php }}?>
        </tbody>
       
    </table>
    <form>
    <p>
    Choose which calculation you desire, enter the relevant population values
    (as decimal fractions)
    for p1 (proportion in population 1) and p2 (proportion in population 2)
    and, if calculating power,
    a sample size (assumed the same for each sample).
    You may also modify α (type I error rate) and the power, if relevant.
    After making your entries, hit the <b>calculate</b> button at the bottom.
    </p>
    <li> <input type="radio" name="calc" value="1" checked="" onclick="blank_ss(this.form)">Calculate Sample Size (for specified Power)
    </li><li> <input type="radio" name="calc" value="2" onclick="blank_ss(this.form)">Calculate Power (for specified Sample Size)
    </li><table>
    <tbody><tr>
    <th align="LEFT">Enter a value for p1:
    </th><td><input type="TEXT" name="p1" size="10">
    </td></tr><tr>
    <th align="LEFT">Enter a value for p2:
    </th><td><input type="TEXT" name="p2" size="10">
    </td></tr></tbody></table>
    <li> <input type="radio" name="sided" value="1">1 Sided Test
    </li><li> <input type="radio" name="sided" value="2" checked="">2 Sided Test
    </li><table>
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
</div>
</div>
<hr>
<?php } ?>
</body></html>