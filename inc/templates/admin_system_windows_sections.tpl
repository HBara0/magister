<div style='margin-top: 10px; '>
    <input type="hidden" value="{$swid}" name="window_id" id="window_id">
    <a {$disable_tablink} id="createtab" class="showpopup" href="#"><img border="0" alt="{$lang->addsection}" src="{$core->settings['rootdir']}/images/addnew.png"> {$lang->addsection}</a>
</div>
<div id="sectionstabs" class=""> <!--template-->
    <ul>
        {$sectionstabs}
    </ul>
    <div id="loadindsection"></div>
    {$section_content}
</div>