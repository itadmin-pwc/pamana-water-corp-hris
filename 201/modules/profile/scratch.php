 <table width="90%" border="0" cellspacing="1" cellpadding="2">
					  <tr> 
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					  </tr>
					  <tr> 
						<td width="26%" class="gridDtlLbl">Address</td>
						<td width="1%" class="gridDtlLbl">:</td>
						<td width="16%" class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($profObj->getContacttypes(),'cmbadd1','cmbadd1',''),'cmbadd1','cmbadd1','class="inputs" style="width:222px;"'); ?></td>
						<td width="57%" class="gridDtlVal"><textarea name="txtadd1" cols="40" rows="3" class="inputs" id="txtadd1"></textarea></td>
					  </tr>
					  <tr> 
						<td rowspan="4" class="gridDtlLbl">Phone Type and Nos</td>
						<td class="gridDtlLbl">:</td>
						<td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($profObj->getContacttypes(),'cmbadd2','cmbadd2',''),'cmbadd2','cmbadd2','class="inputs" style="width:222px;"'); ?></td>
						<td class="gridDtlVal"><textarea name="txtadd2" cols="40" rows="3" class="inputs" id="txtadd2"></textarea></td>
					  </tr>
					  <tr> 
						<td class="gridDtlLbl">:</td>
						<td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($profObj->getContacttypes(),'cmbadd3','cmbadd3',''),'cmbadd3','cmbadd3','class="inputs" style="width:222px;"'); ?></td>
						<td class="gridDtlVal"><textarea name="txtadd3" cols="40" rows="3" class="inputs" id="txtadd3"></textarea></td>
					  </tr>
					  <tr> 
						<td class="gridDtlLbl">:</td>
						<td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($profObj->getContacttypes(),'cmbadd4','cmbadd4',''),'cmbadd4','cmbadd4','class="inputs" style="width:222px;"'); ?></td>
						<td class="gridDtlVal"><textarea name="txtadd4" cols="40" rows="3" class="inputs" id="txtadd4"></textarea></td>
					  </tr>
					  <tr> 
						<td class="gridDtlLbl">:</td>
						<td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($profObj->getContacttypes(),'cmbadd5','cmbadd5',''),'cmbadd5','cmbadd5','class="inputs" style="width:222px;"'); ?></td>
						<td class="gridDtlVal"><textarea name="txtadd5" cols="40" rows="3" class="inputs" id="txtadd5"></textarea></td>
					  </tr>
					</table>