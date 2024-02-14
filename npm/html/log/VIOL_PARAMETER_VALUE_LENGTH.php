<?php
	if ($req_violations['value_length_type']=="max length")
		$type = "max";
	else
		$type = "min";
?>


<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>
	
<tbody>
	<tr>
		<td>Violation Type</td>
		<td><b>Illegal parameter value length (<?php echo ($req_violations['value_length_type']);  ?>)</b></td>
	</tr>
	<tr>
		<td>Parameter Name</td>
		<td><b><span id="parameter_length_value_name"><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['name']), ENT_SUBSTITUTE);  ?></span></b></td>
	</tr>
	<tr>
		<td>Value</td>
		<td><b><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['value']), ENT_SUBSTITUTE);  ?></b></td>
	</tr>
	<tr>
		<td>Expected Length</td>
		<td><b><span id="parameter_expected_length"><?php echo ($req_violations['expected_value_length']);  ?></b></span>
		<button type="button" class="btn btn_float_right btn-sm btn-success learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="illegal_value_length_<?php echo $type;?>">Modify</button>
		</td>
	</tr>
	<tr>
		<td>Actual Length</td>
		<td style="color:red"><b><span id="parameter_detected_length"><?php echo ($req_violations['actual_value_length']);  ?></span></b></td>
	</tr>
	<tr>
		<td>Enforcement</td>
		<td><b><?php echo ($req_violations['parameter_data']['enforcement_level']);  ?></b></td>
	</tr>	
	</tbody>
</table>
