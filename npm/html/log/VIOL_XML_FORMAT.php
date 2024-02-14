
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
			<td>Context</td>
			<td><b><?php echo $req_violations['context'];  ?></b></td>
		</tr>
		<tr>
			<td>Failed Defense</td>
			<td><b><?php echo $req_violations['failed_defense'];  ?></b></td>
		</tr>
		<tr>
		<td>Failed Defense XPath</td>
			<td><b><?php echo $req_violations['failed_defense_xpath'];  ?></b></td>
		</tr>				
		<tr>
		<td>Actual Method</td>
			<td><b><?php echo $req_violations['actual_method'];  ?></b></td>
		</tr>			
		<td>Actual Value</td>
			<td><b><?php echo $req_violations['actual_value'];  ?></b></td>
		</tr>			
		<td>Allowed Value</td>
			<td><b><?php echo $req_violations['allowed_value'];  ?></b></td>
		</tr>			

		<tr>
			<td>Content Profile Data</td>
			<td>
			
				<?php 
					echo 'Content ID = <b>'.$req_violations['content_profile_data']['content_id'].'</b><br>';
					echo 'Content Profile Name = <b>'.$req_violations['content_profile_data']['content_profile_name'].'</b><br>';
					echo 'Content Profile ID = <b>'.$req_violations['content_profile_data']['content_profile_id'].'</b><br>';
					echo 'Specific Desc = <b>'.$req_violations['content_profile_data']['specific_desc'].'</b><br>';
					echo 'Type = <b>'.$req_violations['content_profile_data']['type'].'</b>';
				?>				
			
			</td>
		</tr>
											
	</tbody>
</table>