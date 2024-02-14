<?php 

	$array=[];
	if (is_array($req_violations['metachar_index']))
	{
		$array = $req_violations['metachar_index'];	
		$size = sizeof($req_violations['metachar_index']);
	}	
	else
	{
		array_push($array, $req_violations['metachar_index']);
		$size = 1;
	}
	
?>

<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>

	
	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Illegal meta character in parameter value</b></td>
		</tr>
		<tr>
			<td>Parameter Name</td>
			<td><b><?php echo '<span id="parameter_'.$total_count.'">'.htmlspecialchars(base64_decode($req_violations['param_name']),ENT_SUBSTITUTE).'</span>'; ?></b></td>

		</tr>
			<?php
			if (array_key_exists("wildcard_entity",$req_violations))
			{echo '
				<tr>
					<td>Matched on Wildcard</td>
					<td><b>'.htmlspecialchars($req_violations['wildcard_entity'],ENT_SUBSTITUTE) .'</b></td>
				</tr>';
			}?>
		<tr>
			<td <?php echo 'rowspan='.$size; ?>>Illegal Meta Character</td>
			<?php 
				$count_index=0;
				foreach ($array as $metachars)
				{
				
					echo ' <td><b><span style="display:none" id="metachar_'.$total_count.'">'. htmlspecialchars($metachars). '</span><span style="color:red">'. htmlspecialchars(chr($metachars)). '</span></b>';
					echo '
					<button hidden type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="attack_metachar_value" value="'.$total_count.'">Disable MetaChar </button>
						</td>
					</tr>';

					$count_index++;
					$total_count++;
					if ($count_index<sizeof($req_violations['metachar_index']))
					{
						echo ' <tr>';
					}
				}
			?>
		
		<tr>
			<td>Enforcement</td>
			<td><b><?php echo (($req_violations['enforcement_level']));  ?></b></td>
		</tr>
										
		</tbody>

</table>


