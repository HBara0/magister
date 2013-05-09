<tr id="{$question_rowid}" class="altrow2">
    <td style="border-bottom:1px solid #CCC;">    
        <table border="0" cellspacing="1" cellpadding="1" width='100%'>
            <tr> 
                <td>{$lang->question}</td>
                <td>
                    <input name="section[{$section_rowid}][questions][{$question_rowid}][question]"  value="{$section[section_rowid][questions][question_rowid][question]}" type="text" size="40" autocomplete="on" required="required">
                    <div style='float:right; cursor: move;' class="ui-state-default"><span class="questions-sort-icon ui-icon ui-icon-arrowthick-2-n-s"></span></div>
                </td>
            </tr>

            <tr>
                <td>{$lang->questiondesc}</td>
                <td><input name="section[{$section_rowid}][questions][{$question_rowid}][description]"  value="{$section[section_rowid][questions][question_rowid][description]}" type="text" size="40" autocomplete="on"></td>
            </tr>

            <tr>
                <td>{$lang->isrequired}</td>
                <td>{$radiobuttons[isRequired]}</td>
            </tr>
            <tr>
                <td>{$lang->questiontype}</td>                                                                        
                <td><select tabindex="" name="section[{$section_rowid}][questions][{$question_rowid}][type]" id="section_[{$section_rowid}]_[questions]_[{$question_rowid}]_[type]" required="required">{$question_types_options}</select></td>
            </tr>
            <tr>
                <td>{$lang->commentsfieldtitle}</td>
                <td><input name="section[{$section_rowid}][questions][{$question_rowid}][commentsFieldTitle]" value="{$section[section_rowid][questions][question_rowid][commentsFieldTitle]}"  title="{$lang->commentsFieldTitle_tip}" type="text" size="40"></td>
            </tr>
            <tr>
                <td>{$lang->commentsfieldsize}</td>
                <td><input accept="numeric" id="commentsFieldSize" value="{$section[section_rowid][questions][question_rowid][commentsFieldSize]}" name="section[{$section_rowid}][questions][{$question_rowid}][commentsFieldSize]" type="text" size="10" title="{$lang->commentsfieldsize_tip}" maxlength="3"><span id="fieldsizevalidate"></span></td>
            </tr>
            <tr id="section[{$section_rowid}][questions][{$question_rowid}][fieldSize_container]" style="display:none;">
                <td>{$lang->fieldsize}</td>
                <td><input accept="numeric" name="section[{$section_rowid}][questions][{$question_rowid}][fieldSize]" type="text" value="" size="5"></td>
            </tr>
            <tr id="section[{$section_rowid}][questions][{$question_rowid}][validationType_container]" style="display:none;">
                <td>{$lang->validationtype}</td>
                <td>
                    <select id="section_[{$section_rowid}]_[questions]_[{$question_rowid}]_[validationType]" name="section[{$section_rowid}][questions][{$question_rowid}][validationType]">
                        <option value="" selected='selected'>&nbsp;</option>
                        <option value="minchars">{$lang->minchars}</option>
                        <option value="maxchars">{$lang->maxchars}</option>
                        <option value="email">{$lang->emailonly}</option>
                        <option value="numeric">{$lang->numbersonly}</option>
                    </select>
                </td>
            </tr>
            <tr id="section_[{$section_rowid}]_[questions]_[{$question_rowid}]_[validationCriterion]" style="display:none;">
                <td>{$lang->validationcriterion}</td>
                <td><input type="text" name="section[{$section_rowid}][questions][{$question_rowid}][validationCriterion]" accept="numeric"></td>
            </tr>
            <tr id="section[{$section_rowid}][questions][{$question_rowid}][choices_container]" style="display:none;">
                <td>{$lang->choices}</td>
                <td><textarea name="section[{$section_rowid}][questions][{$question_rowid}][choices]" title="{$lang->choices_tip}" cols="33" rows="5"></textarea></td>
            </tr>
        </table>
    </td>
</tr>