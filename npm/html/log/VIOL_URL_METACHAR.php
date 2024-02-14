
<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Illegal URL metacharacter</b></td>
		</tr>
		<tr>
			<td>URL</td>
			<td><b><?php echo htmlspecialchars(base64_decode($req_violations['uri']), ENT_SUBSTITUTE);  ?></b></td>
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
		<?php 
			if (array_key_exists("wildcard_entity",$req_violations))
			{echo '
				<tr>
					<td>Wildcard entry</td>
					<td><b>'.($req_violations['wildcard_entity']) .'</b></td>
				</tr>';
			}
		?>												
		</tbody>						
</table>					
