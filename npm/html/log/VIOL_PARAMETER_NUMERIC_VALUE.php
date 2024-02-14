

<?php


###  VIOL_PARAMETER_NUMERIC_VALUE provides different values for Min or Max violations.. On this part of the script we will identify if it is Min or Max violation and present the relevant fields.

if(array_key_exists("expected_multiple_of_value",$req_violations))
	$multiple = true;
else
	$multiple = false;

if(array_key_exists("expected_min_range",$req_violations))
{
	$title_short = "Min";
	$title_long = "Mininum";
	$range = "expected_min_range";
	$exclusive = "expected_exclusive_minimum";
}
else
{
	$title_short = "Max";
	$title_long = "Maximum";
	$range = "expected_max_range";
	$exclusive = "expected_exclusive_maximum";
}

?>

<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>
	
<tbody>
	<tr>
		<td>ViolationType</td>
		<td><b>Illegal parameter numeric value <?php if($multiple) echo "(Multiple Of)"; ?></b></td>
	</tr>
	<tr>
		<td>Parameter Name</td>
		<td><b><span id="parameter_numeric_value_name"><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['name']), ENT_SUBSTITUTE);  ?></span></b></td>
	</tr>
	<tr>
		<td>Value</td>
		<td style="color:red"><b><span id="parameter_numeric_value_detected"><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['value']), ENT_SUBSTITUTE);  ?></span></b></td>
	</tr>
	<?php 
		 if(!$multiple)
		 	echo ' 
				<tr>
					<td>Expected '. $title_short .' Range</td>
					<td><b><span id="parameter_numeric_value_expected">'. (int)$req_violations[$range] .'</span></b>
					<button type="button" class="btn btn_float_right btn-sm btn-success learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="numeric_value_'.strtolower($title_short).'">Modify</button>
					</td>
				</tr>
				<tr>
					<td>Exclusive '. $title_short .'</td>
					<td><b><span id="value_exclusive">'. $req_violations[$exclusive] .'</span></b></td>
				</tr>';

		if($multiple)
			echo ' 
				<tr>
					<td>Expected Multiple Of</td>
					<td><b><span id="expected_multiple_of_value">'. (int)$req_violations["expected_multiple_of_value"] .'</span></b>
						<button type="button" class="btn btn_float_right btn-sm btn-success learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="numeric_value_multipleof">Modify</button>
					</td>
				</tr>';

	?>
	<tr>
		<td>Enforcement</td>
		<td><b><?php echo ($req_violations['parameter_data']['enforcement_level']);  ?></b></td>
	</tr>	
	</tbody>
</table>
