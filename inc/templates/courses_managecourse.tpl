<div class="row">
    <h1>{$lang->managecourse}</h1>
</div>
<form  action="#" method="post" id="perform_courses/managecourse_Form" name="perform_courses/managecourse_Form">
    <input type="hidden" name="course[cid]" value="{$course[cid]}">
    <div class="form-group-lg">
        <label for="code" style="font-weight: bold">{$lang->coursecode}<span style="color:red"> *</span></label>
        <input required="required" name='course[code]' value='{$course[code]}' type="text" class="form-control" id="code" placeholder="{$lang->coursecode}">
    </div>
    <div class="form-group-lg">
        <label for="title" style="font-weight: bold">{$lang->coursetitle}<span style="color:red"> *</span></label>
        <input required="required" name='course[title]' value='{$course[title]}' type="text" class="form-control" id="title" placeholder="{$lang->coursetitle}">
    </div>
    <label>{$lang->teacher}</label>
    <div class="form-group-lg">
        {$teacher_list}
    </div>
    <label>{$lang->program}</label>
    <div class="form-group-lg">
        {$programs_list}
    </div>
    <div class="form-group-lg">
        <label for="googledrivefolderlink">{$lang->googledrivefolderlink}</label>
        <input name='course[folderUrl]' value='{$course[folderUrl]}' type="url" class="form-control" id="googledrivefolderlink" placeholder="{$lang->googledrivefolderlink}">
    </div>
    <label>{$lang->isActive}</label>
    <div class="form-group-lg">
        {$isactive_list}
    </div>
    <div class="form-group-lg">
        <label for="description">{$lang->description}</label>
        <div style="display:block;">
            <textarea name="course[description]" cols="100" rows="6" id='description' class="basictxteditadv">
                {$course[description]}
            </textarea>
        </div>
    </div>
    {$lecture_section}
    {$studentsubscription_section}
    <input type="submit" value="{$lang->savecaps}" id="perform_courses/managecourse_Button" class="button"/>
    <div id="perform_courses/managecourse_Results"></div>

</form>
<div class="modal fade" id="courses_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>