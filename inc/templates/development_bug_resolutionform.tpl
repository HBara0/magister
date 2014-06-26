<div>
    <div class="thead">Resolution</div>
    <form name="perform_development/viewbug_Form" method="post" id="perform_development/viewbug_Form">
        <input type='hidden' value="do_resolve" name="action">
        <input type="hidden" value='{$bug->dbid}' name="dbid">
        <div>Fixed <input type="checkbox" name='isFixed' value='1'></div>
        <div>Commit Hash <input type='text' name='commitHash' size='40'></div>
        <div>Commit Message<br /><textarea cols="50" rows="5" name='commitMsg'></textarea></div>
        <div>Fixed in Version <input type='number' name='fixedVersion' step='any' min="1"></div>

        <input type="button" id='perform_development/viewbug_Button' value="{$lang->savecaps}">
        <div id='perform_development/viewbug_Results'></div>
        </from>
</div>