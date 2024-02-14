
<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col class="col-xs-2">
		<col class="col-xs-6">
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Illegal meta character in header</b></td>
		</tr>
		<tr>
			<td>Header</td>
			<td><b><?php echo htmlspecialchars(($req_violations['header']['header_name']), ENT_SUBSTITUTE);  ?></b></td>
		</tr>
		<tr>
			<td>Illegal Meta Character</td>
			
			<?php
					echo ' <td><b><span style="display:none" id="metachar_'.$total_count.'">'. $req_violations['metachar_index']. '</span><span style="color:red">'. htmlspecialchars(chr($req_violations['metachar_index'])). '</span></b>';
					echo '
					<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="attack_metachar_value" value="'.$total_count.'" hidden>Disable MetaChar </button>
						</td>
					</tr>';

					$total_count++;
			?>
			
		</tr>
	</tbody>						
	
</table>					
