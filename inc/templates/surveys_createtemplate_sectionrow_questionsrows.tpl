<tr id="{$question_rowid}" class="altrow2">
    <td style="border-bottom:1px solid #CCC;">    
        <table border="0" cellspacing="1" cellpadding="1" >
            <tr>
                <td width="30%">{$lang->sequence}</td>
                <td width="70%">
                    <input accept="numeric" name="section[{$section_rowid}][questions][{$question_rowid}][sequence]" id="sequence_{$section_rowid}_{$question_rowid}"   value="{$sequence}"  type="text" size="2" maxlength="2"></td>
            </tr>
            <tr> 
                <td>{$lang->question}</td>
                <td><input name="section[{$section_rowid}][questions][{$question_rowid}][question]"  value="{$section[section_rowid][questions][question_rowid][question]}" type="text" size="40" autocomplete="on"></td>
            </tr>

            <tr>
                <td>{$lang->questiondesc}</td>
                <td><input name="section[{$section_rowid}][questions][{$question_rowid}][question_description]"  value="{$section[section_rowid][questions][question_rowid][question_description]}" type="text" size="40" autocomplete="on"></td>
            </tr>

            <tr>
                <td>{$lang->isrequired}</td>
                <td>{$radiobuttons[isRequired]}</td>
            </tr>
            <tr>
                <td>{$lang->questiontype}</td>                                                                        
                <td><select tabindex="" name="section[{$section_rowid}][questions][{$question_rowid}][type]" id="section_[{$section_rowid}]_[questions]_[{$question_rowid}]_[type]">{$question_types_options}</select></td>
            </tr>
            <tr>
                <td>{$lang->commentsfieldtitle}</td>
                <td><input name="section[{$section_rowid}][questions][{$question_rowid}][commentsFieldTitle]"  value="{$section[section_rowid][questions][question_rowid][commentsFieldTitle]}"  title="{$lang->commentsFieldTitle_tip}" type="text"></td>
            </tr>
            <tr>
                <td>{$lang->commentsgieldsize}</td>
                <td><input accept="numeric" id="commentsFieldSize" value="{$section[section_rowid][questions][question_rowid][commentsFieldSize]}" name="section[{$section_rowid}][questions][{$question_rowid}][commentsFieldSize]" type="text" size="10" title="{$lang->commentsfieldsize_tip}" maxlength="3"><span id="fieldsizevalidate"></span></td>
            </tr>
            <tr id="section[{$section_rowid}][questions][{$question_rowid}][fieldSize_container]" style="display:none;">
                <td width="30%">{$lang->fieldsize}</td>
                <td><input accept="numeric" name="section[{$section_rowid}][questions][{$question_rowid}][fieldSize]" type="text" value="" size="5"></td>
            </tr>
            <tr id="section[{$section_rowid}][questions][{$question_rowid}][validationType_container]" style="display:none;">
                <td width="30%">{$lang->validationtype}</td>
                <td>
                    <select  id="section[{$section_rowid}][questions][{$question_rowid}][validationType]" name="section[{$section_rowid}][questions][{$question_rowid}][validationType]">
                        <option value="" selected='selected'>&nbsp;</option>
                        <option value="minchars">{$lang->minchars}</option>
                        <option value="maxchars">{$lang->maxchars}</option>
                        <option value="email">{$lang->emailonly}</option>
                        <option value="numeric">{$lang->numbersonly}</option>
                    </select>
                </td>
            </tr>
            <tr id="section[{$section_rowid}][questions][{$question_rowid}][choices_container]" style="display:none;">
                <td width="30%">{$lang->choices}</td>
                <td><textarea name="section[{$section_rowid}][questions][{$question_rowid}][choices]" title="{$lang->choice_tip}" cols="33" rows="5"></textarea></td>
            </tr>
        </table>
    </td>
</tr>
