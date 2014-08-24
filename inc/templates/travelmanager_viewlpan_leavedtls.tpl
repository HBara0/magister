<div id="leavedetials" style=" width:100%; clear: both;display: block; height:20%; margin-bottom: 25px;">
    <span style="font-weight: bold; font-size: 14px;">{$lang->leavedetails}:</span>
    <div style="height: 100%;vertical-align: middle;left: -5px;background-color: #92D050; z-index: 1;line-height: 32px;padding:5px; font-style: oblique;font-size:14px;color: #000;position: relative;">
        {$leave_title}<span><a target="_blank" href="index.php?module=attendance/listleaves&action=takeactionpage&requestKey={$this->get()[requestKey]}"> {$lang->viewleave}</a></span>
        <div style="position: relative;">{$lang->purpose}: {$this->get_purpose()->get()[name]}</div>
        <div style="position: relative;">{$lang->segment}: {$this->get_segment()->get()[title]}</div>
    </div>

</div>