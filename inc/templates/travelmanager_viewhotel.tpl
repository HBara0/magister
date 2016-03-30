<h1>{$lang->viewhotel} : {$hotel['name']}</h1>
{$approve}
<input type="hidden" name="hotel[tmhid]" value='{$hotel['tmhid']}'>
<div class="row bkgcolor_greygradient">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"><label>{$lang->isapproved} : </label> &nbsp {$approved}</div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"><label>{$lang->iscontracted} : </label>&nbsp {$contracted}</div>
</div>
<div class="row bkgcolor_greygradient">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> <label> {$lang->hotelname}</label> : {$hotel['name']}</div>
</div>
<div class="row bkgcolor_greygradient">
    <div class="col-xs-12 col-sm-6 col-md-6  col-lg-6 "> <label >{$lang->city}</label> : {$hotel['cityname']}</div>
    <div class="col-xs-12 col-sm-6  col-md-6  col-lg-6 "> <label >{$lang->country}</label> : {$hotel['countryname']}</div>
</div>
<div class="row bkgcolor_greygradient">
    <div class="col-xs-12 col-sm-6 col-md-6  col-lg-6 "> <label >{$lang->addressline1}</label> : {$hotel['addressLine1']}</div>
    <div class="col-xs-12 col-sm-6  col-md-6  col-lg-6 "> <label >{$lang->addressline2}</label> : {$hotel['addressline2']}</div>
</div>
<div class="row bkgcolor_greygradient">
    <div class="col-xs-12 col-sm-6 col-md-6  col-lg-6 "> <label >{$lang->postcode}</label> : {$hotel['postCode']}</div>
    <div class="col-xs-12 col-sm-6  col-md-6  col-lg-6 "> <label >{$lang->pobox}</label> : {$hotel['poBox']}</div>
</div>
<div class="row bkgcolor_greygradient">
    <div class="col-xs-12 col-sm-6 col-md-6  col-lg-6 "> <label >{$lang->phone}</label> : {$hotel['phone']}</div>
    <div class="col-xs-12 col-sm-6  col-md-6  col-lg-6 "> <label >{$lang->fax}</label> : {$hotel['fax']}</div>
</div>
<div class="row bkgcolor_greygradient">
    <div class="col-xs-12 col-sm-6 col-md-6  col-lg-6 "> <label >{$lang->website}</label> : {$hotel['website']}</div>
    <div class="col-xs-12 col-sm-6  col-md-6  col-lg-6 "> <label >{$lang->mainemail}</label> : {$hotel['mainEmail']}</div>
</div>
<div class="row bkgcolor_greygradient">
    <div class="col-xs-12 col-sm-6 col-md-6  col-lg-6 "> <label >{$lang->contactperson}</label> : {$hotel['contactPerson']}</div>
    <div class="col-xs-12 col-sm-6  col-md-6  col-lg-6 "> <label >{$lang->contactemail}</label> : {$hotel['contactEmail']}</div>
</div>
<div class="row bkgcolor_greygradient">
    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 "> <label >{$lang->averageprice}</label> : {$hotel['avgPrice']}</div>
    <div class="col-xs-12 col-sm-3  col-md-3 col-lg-3"> <label >{$lang->currency}</label> : {$hotel['currency_output']}</div>
    <div class="col-xs-12 col-sm-3  col-md-3 col-lg-3 "> <label >{$lang->distancefromoffice}</label> : {$hotel['distance']}</div>
</div>
<div class="row bkgcolor_greygradient">
    <div class="col-xs-12 col-sm-12 col-md-12  col-lg-12 "> <label >{$lang->mgmtreview}</label> : {$hotel['mgmtReview']}</div>
</div>
<div class="row bkgcolor_greygradient">
    <div class="col-xs-12 col-sm-6 col-md-6  col-lg-6 "> <label >{$lang->createdby}</label> : {$hotel['createdBy_output']}</div>
    <div class="col-xs-12 col-sm-6  col-md-6  col-lg-6 "> <label >{$lang->approvedby}</label> : {$hotel['approvedBy_output']}</div>
</div>
