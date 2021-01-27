<?php
define( "FROM_INDEX", true );

include_once( "init.php" );
?>

<html><head>

<title>Power/Sample Size Calculator</title>

<script language="JavaScript" src="formula.js"></script>

</head

><body>
<?php 
foreach($tests as $test){?>
<h2><?php echo $test['name'];?></h2>
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
<hr>
<?php } ?>
</body></html>