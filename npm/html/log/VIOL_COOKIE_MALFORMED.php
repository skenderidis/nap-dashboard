
			
	
	<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
		<colgroup>
			<col style="width: 150px">
			<col>
		</colgroup>

		<tbody>
			<tr>
				<td>Violation Type</td>
				<td><b>Malformed Cookie</b><input type="text" class="hidden" id="violation_malformed_cookie" value="VIOL_COOKIE_MALFORMED"</input>
				<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="malformed_cookie">Disable</button>

				</td>
			</tr>
			<tr>
				<td>Violation Description</td>
				<td > <b><?php echo (($req_violations['specific_desc']));  ?></b>
				</td>
			</tr>
			<tr>
				<td>Cookie name/value</td>
				<td> 
					<b>
						<?php 
							$temp_attack = base64_decode($req_violations['buffer']);  
							$temp_start = substr($temp_attack, 0, $req_violations['offset'] );
							$temp_violation = substr($temp_attack, $req_violations['offset'], 1 );
							$temp_end = substr($temp_attack, $req_violations['offset']+1 );
							echo htmlspecialchars($temp_start, ENT_SUBSTITUTE). "<span style='color:red; border:1px solid red;'>" . htmlspecialchars($temp_violation, ENT_SUBSTITUTE) . "</span>" . $temp_end;  
						?>
					</b>
				</td>
			</tr>

		</tbody>						
	</table>					
