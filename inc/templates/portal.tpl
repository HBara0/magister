<div style="display:inline-block;float:left;">
    <div class="portalbox">
        <div class="portalbox_header">
            {$lang->weheretohelp}
        </div>
        <div>
            {$lang->adminreadyassist}
            <ul type="disc">
                <li><a href="#" id="showpopup_submitsupportticket" class ="showpopup">{$lang->submitsupportticket}</a></li>
                <li>{$lang->callonnum}</li>
            </ul>
            <div align="center"><a href="skype:zaher.reda"><img src="images/skype.gif" border="0" /></a></div>
        </div>
    </div>
    <div class="portalbox">
        <div class="portalbox_header">
            {$lang->worldtime}
        </div>
        <div>
            <ul>
                {$timezones_list}
            </ul>
        </div>
    </div>

    <div class="portalbox">
        <div class="portalbox_header">
            {$lang->quickintro}Quick Intro
        </div>
        <div>
            <iframe src="https://docs.google.com/file/d/0B8G025lo12QnZVFVS25iYldzY2s/preview" width="180" height="100"></iframe>
        </div>
    </div>
</div>
<!-- <div class="portalbox">
     <div class="portalbox_header">
{$lang->currencyconvertor}
</div>
<div>
{$lang->from} {$currencyfrom_selectlist} {$lang->to} {$currencyto_selectlist}<br />
<span id="currencyconvert_Results"></span>
</div>
</div>-->

<div style="width: 63%; float:left;">
    <h1>{$lang->welcometoocos}</h1>
    {$lang->mainmessage}

    <p>
        <strong>{$lang->featuredmodules}:</strong><br />
    <div>
        {$portalicons}
        <span style="display:inline-block; width: 100px; height:100px; text-align:center; vertical-align:top;">
            <div class="portalbox_calendar" style="width: 100px; height:96px; margin: 0px; margin-bottom: 4px; padding-top: 0px; min-height: 90px;">
                <div style="padding-top: 8px;">
                    <a href="index.php?module=calendar/home" style="color:#FFF;">{$current_date[weekday]}</a>
                </div>
                <div style="font-size:30px;">
                    <a href="index.php?module=calendar/home" style="color:#FFF;">{$current_date[mday]}</a>
                </div>
                <div>
                    <a href="index.php?module=calendar/home" style="color:#FFF;">{$current_date[monthname]} {$current_date[year]}</a>
                </div>
            </div>
            <a href="index.php?module=calendar/home">{$lang->calendar}</a>
        </span>
    </div>
</p>
<p><hr /></p>
<span class="subtitle">{$lang->systemnews}</span>
{$portal_news}
</div>
<div style="width:200px; float:right; margin:10px;">
    <div class="portalbox_calendar">
        <div>
            {$current_date[weekday]}
        </div>
        <div style="font-size:50px;">
            <a href="index.php?module=calendar/home" style="color:#FFF;">{$current_date[mday]}</a>
        </div>
        <div>
            {$current_date[monthname]} {$current_date[year]}
        </div>
    </div>
    <div class="portalbox">
        <div class="portalbox_header">
            <a href="index.php?module=attendance/listleaves">{$lang->attendance}</a>
        </div>
        <div>
            <ul>
                <li>{$total_leaves} {$lang->leavestotal}</li>
                <li>{$leaves_approved} {$lang->leavesapproved}</li>
                <li>{$lang->leavescurrentmonth}</li>
            </ul>
        </div>
    </div>
    <div class="portalbox">
        <div class="portalbox_header">{$lang->latestfxrates}</div>
        <div>{$lang->usdfxrate}<ul>
                {$currencysrates_list}
            </ul>
        </div>
    </div>
    {$reporting_box}
</div>

<div id="popup_submitsupportticket" title="{$lang->weheretohelp}">
    <form action="#" method="post" id="perform_contactsupport_portal/portal_Form" name="perform_contactsupport_portal/portal_Form">
        <input type="hidden" value="do_submitsupportticket" name="action"/>
        <em>{$lang->submitticketexplain}</em><br /><br />
        <div style="font-weight:bold; width:20%; display:inline-block;">{$lang->ticketsummary}</div><div style="width:75%; display:inline-block;"><input type="text" id="subject" name="subject" size="53" tabindex="1"/></div>
        <div style="font-weight:bold; width:20%; display:inline-block; vertical-align:top;">{$lang->ticketdescription}</div><div style="width:75%; display:inline-block;"><textarea id="message" name="message" tabindex="2" cols="50" rows="15"></textarea></div>
        <hr />
        <input type="button" class="button" id="perform_contactsupport_portal/portal_Button" value="{$lang->submitticket}" />
    </form>
    <div id="perform_contactsupport_portal/portal_Results" name="perform_contactsupport_portal/portal_Results"></div>
</div>
