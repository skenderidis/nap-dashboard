<?php
$type = "Not Available";

switch ($req_violations['expected_data_type']) {
	case 256:
		$type = "boolean";
		break;
	case 64:
		$type = "email";
		break;	
	case 128:
		$type = "phone";
		break;	
	case 4:
		$type = "decimal";
		break;
	case 2:
		$type = "integer";
		break;
	default:
		$type = "alpha-numeric";
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
		<td><b>Illegal parameter data type</b></td>
	</tr>
	<tr>
		<td>Parameter Name</td>
		<td style="color:red"><b><span id="parameter_datatype_name"><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['name']), ENT_SUBSTITUTE);  ?></span></b></td>
	</tr>
	<tr>
		<td>Expected Data Type</td>
		<td><b><span id="expected_data_type"><?php echo $type; ?></span></b>
		<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="datatype">Change Type</button>

		</td>
	</tr>
	<tr>
		<td>Parameter value</td>
		<td><b><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['value']), ENT_SUBSTITUTE);  ?></b></td>
	</tr>	
	<tr>
		<td>Enforcement</td>
		<td><b><?php echo ($req_violations['parameter_data']['enforcement_level']);  ?></b></td>
	</tr>
	</tbody>
</table>
