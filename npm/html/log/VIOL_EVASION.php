
<table class="table table-bordered table-striped table-violation" style=" font-size:12px; ">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>

		<tbody>
			<tr>
				<td>Violation Type</td>
				<td><b>Evasion Technique Detected</b></td>
			</tr>
			<tr>
				<td>Details</td>
				<td><b><?php 
					
					$temp_attack = base64_decode($ev_violations['buffer']);  
					$temp_start = substr($temp_attack, 0, $ev_violations['offset'] );
					$temp_violation = substr($temp_attack, $ev_violations['offset'], $ev_violations['length'] );
					$temp_end = substr($temp_attack, $ev_violations['offset']+$ev_violations['length'] );
					echo htmlspecialchars($temp_start). "<span style='color:red; border:1px solid red;'>" . htmlspecialchars($temp_violation) . "</span>" . $temp_end;
					?></b>
				</td>			
			 </tr>
			 <tr>
				<td colspan=2 ><i>You can disable this violation from the "Sub-violations" section</i></td>
			</tr>		
		</tbody>	

</table>
