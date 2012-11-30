
 <div id="popup_createchemical"  title="{$lang->createchemical}" >
    
	<form name='add_chemical_sourcing/managesupplier_Form' id='add_chemical_sourcing/managesupplier_Form' method="post">
			<input type="hidden" id="action" name="action" value="do_createchemical" /> 

        	<div style="display:table-row">
            	<div style="display:table-cell; width:100px; vertical-align:middle;">{$lang->casno}</div>
                <div style="display:table-cell;padding:3px"><input name="supplier[chemcialsubstances][casNum]" type="text"></div>
            </div>
            
            	<div style="display:table-row">  
                <div style="display:table-cell">{$lang->chemicalname}</div>
					<div style="display:table-cell; padding:3px" ><input name="supplier[chemcialsubstances][name]" size="40" type="text"></div>
				</div>
                
                <div style="display:table-row">  
                <div style="display:table-cell; vertical-align:top;">{$lang->chemicalsynonym}</div>
					<div style="display:table-cell;padding:3px">
					  <textarea  name="supplier[chemcialsubstances][synonyms]" cols="40" rows="5"></textarea>
					</div>
				</div>
  			<div class="smalltext">{$lang->seperatesynonym}</div>
  <hr>
  
      		<div style="display:table-row">  
                <div style="display:table-cell">   
                    <input type="button" id="add_chemical_sourcing/managesupplier_Button" class="button" value="{$lang->add}"/>
                    <input type="reset" class="button" value="{$lang->reset}" />
                </div>
           </div>
         
  	</form>
    <div id="add_chemical_sourcing/managesupplier_Results"></div>
    </div>
