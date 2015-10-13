<div id="segmentdetail_{$this->data[tmpsid]}" style=" border: 1px solid white; padding: 5px 0px 0px 5px; margin-top:10px; margin-bottom: 10px; width:100%; clear:both;display:block; line-height:28px; background-color:white; height:auto;">
    <div style="position:relative;height:48px;display: block;border-bottom: 1px dashed #666; font-weight: bold; font-size: 14px; padding:5px; background-color: #92D050 ">
        <span style="position: absolute;width: 0px;height: 0px;border-top: 24px solid transparent;  border-bottom: 24px solid transparent;border-left: 25px solid white;margin-left:3px;"></span>
        <span style="overflow: hidden;white-space: nowrap; text-overflow: ellipsis;  margin:35px;">{$destination_cities}</span><br/>
        <span style="overflow: hidden;white-space: nowrap; text-overflow: ellipsis;  margin:35px;">{$segmentdate}</span>
    </div>
    <h3>{$lang->transportations}</h3>
    <div style="width:100%;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: greenyellow"">{$segment_transpdetails}</div>
    <h3>{$lang->accomodation}</h3>
    <div style="width:100%;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: greenyellow">{$segment_accomdetails}</div>
    <h3>{$lang->additionalexpenses}</h3>
    <div style="width:100%;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: greenyellow">{$additional_expenses_details}</div>
    <h3>{$lang->amountneededinadvance}</h3>
    <div style="width:100%;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: greenyellow">{$segment_advancedpayments}</div>
</div>