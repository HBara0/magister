<div id="popup_mktintldetails" title="{$lang->detlmrktbox}"> 
    <table width="100%" class="datatable">
        <tr>
            <td><strong>{$lang->brand}</strong></td>
            <td>{$mrktintl_detials[brand]} </td>
        </tr>
        <tr>
            <td><strong>{$lang->endproduct}</strong></td>
            <td>{$mrktintl_detials[endproduct]}</td>
        </tr>
        <tr>
            <td><strong>{$lang->annualpotential}</strong></td>
            <td>{$mrktintl_detials[potential]}</td>
        </tr>
        <tr>
            <td><strong>{$lang->marketshare}</strong></td>
            <td>{$mrktintl_detials[mktSharePerc]}</td>
        </tr>
        <tr>
            <td><strong>{$lang->marketshareqty}</strong></td>
            <td>{$mrktintl_detials[mktShareQty]}</td>
        </tr>  
        <tr>
            <td><strong>{$lang->price}</strong></td>
            <td>{$mrktintl_detials[unitPrice]}</td>
        </tr> 

    </table>
              <table>{$marketintelligencedetail_competitors}</table>  
            <div style="padding:8px;"><input class="button" value="{$lang->close}" id="hide_popupBox" type="button" onclick="$('#popup_mktintldetails').dialog('close')"></div>
</div>