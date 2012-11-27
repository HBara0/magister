<div style="display:inline-block" id="contactdetails"  class="border_left">
    <div style="display:table-row;">
   	 <div  style="display:table-cell; margin:10px;"><strong>{$lang->contactdtails}</strong></div>
    </div>
    
    <div style="display:table-row;">
     <div  style="display:table-cell;">{$lang->fulladress}</div>
        <div class="detailsvalue" style="display:table-cell;">{$potential_supplier_details[fulladress]}</div>
    </div>
    <div style="display:table-row;">
   	 <div  style="display:table-cell;">{$lang->pobox}</div>
  	  <div class="detailsvalue" style="display:table-cell;">{$potential_supplier_details[poBox]}</div>
    </div>
    <div style="display:table-row;">
   	 <div  style="display:table-cell;">{$lang->phone1}</div>
   	 <div class="detailsvalue" style="display:table-cell;">{$potential_supplier_details[phone1]}</div>
    </div>
    <div style="display:table-row;">
   	 <div  style="display:table-cell;">{$lang->fax}</div>
   	 <div class="detailsvalue" style="display:table-cell;">{$potential_supplier_details[fax]}</div>
    </div>
    <div style="display:table-row;">
   	 <div  style="display:table-cell;">{$lang->email}</div>
   	 <div class="detailsvalue" style="display:table-cell;">{$potential_supplier_details[mainEmail]}</div>
    </div>
    <div style="display:table-row;">
   	 <div  style="display:table-cell;">{$lang->website}</div>
   	 <div class="detailsvalue" style="display:table-cell;">{$potential_supplier_details[website]}</div>
    </div>
    <div style="display:table-row;">
        <div  style="display:table-cell;margin-bottom:15px"></div>
        </div>
      <div style="display:table-row;">
        <div  style="display:table-cell;"><strong>{$lang->segments}</strong></div>   </div>
  	 	  <div style="display:table-row;">
        <div style="display:table-cell;"> $segment_data</div>
   </div>
   
    <div style="display:table-row;">
        <div style="display: table-cell;width:0px;"> 
     {$contactsupplier_form}
         </div>
    </div>

</div>

<div class="border_left" style="display:inline-block; margin-left:40px; vertical-align:top;" >
</div>
<div style="display:inline-block; margin-left:40px; vertical-align:top;clear:right;" id="contactpersons">
<div style="display:table-row;">
	<div  style="display:table-cell;"><strong>{$lang->contactperson}</strong></div>
    </div>
    <div style="display:table-row;">
        	<div  style="display:table-cell;">{$contact_person_data}</div>
     </div>
            
       <div style="display:table-row;">
        <div  style="display:table-cell; margin-top:10px;"><strong>{$langactivityarea}</strong></div>
         </div>
  	 	  <div style="display:table-row;">
        <div style="display:table-cell;">{$activity_area_data}</div>
   </div>
</div>