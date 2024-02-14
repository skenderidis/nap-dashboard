
<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Malformed XML data</b></td>
		</tr>
		<tr>
			<td>Object Name</td>
			<td><b><?php echo htmlspecialchars(base64_decode($req_violations['object_data']['object']), ENT_SUBSTITUTE);  ?></b></td>
		</tr>
		<tr>
			<td>Object pattern</td>
			<td><b><?php echo $req_violations['object_data']['object_pattern'];  ?></b></td>
		</tr>		
		<tr>
			<td>Content Profile Data</td>
			<td>
			
				<?php 
					echo 'Content ID = <b>'.$req_violations['content_profile_data']['content_id'].'</b><br>';
					echo 'Content Profile Name = <b>'.$req_violations['content_profile_data']['content_profile_name'].'</b><br>';
					echo 'Content Profile ID = <b>'.$req_violations['content_profile_data']['content_profile_id'].'</b><br>';
					echo 'Specific Desc = <b>'.$req_violations['content_profile_data']['specific_desc'].'</b><br>';
					echo 'Fault Detail = <b>'.$req_violations['content_profile_data']['fault_detail'].'</b><br>';
					echo 'Buffer = <b>'.htmlspecialchars(base64_decode($req_violations['content_profile_data']['buffer']), ENT_SUBSTITUTE).'</b>';
				?>				
			
			</td>
		</tr>

											
	</tbody>
</table>