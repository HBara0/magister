<div id="leavedetials" style=" width:100%; clear: both;display: block; height:20%;">
    <span style="font-weight: bold; font-size: 14px;">{$lang->leavedetails}:</span>

    <div style="height: 100%;vertical-align: middle;left: -5px;background-color: lightgray; z-index: 1;line-height: 32px; font-style: oblique;font-size:14px;color: #000;position: relative;">
        {$leave_title}<span> click to view leave</span>
        <div style="position: relative;">{$lang->purpose}: {$this->get_purpose()->get()[name]}</div>
        <div style="position: relative;">{$lang->segment}: {$this->get_segment()->get()[title]}</div>
    </div>

</div>