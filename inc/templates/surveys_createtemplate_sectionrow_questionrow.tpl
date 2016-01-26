<tr id="{$question_rowid}" class="altrow2">
    <td style="border-bottom:1px solid #CCC;">
        <table border="0" cellspacing="1" cellpadding="1" width='100%'>
            <tr>
                <td>{$lang->question}</td>
                <td>
                    <input name="section[{$section_rowid}][questions][{$question_rowid}][question]"  value="{$section[$section_rowid][questions][$question_rowid][question]}" type="text" size="40" autocomplete="on" required="required">
                    <div style='float:right; cursor: move;' class="ui-state-default"><span class="questions-sort-icon ui-icon ui-icon-arrowthick-2-n-s"></span></div>
                </td>
            </tr>

            <tr>
                <td>{$lang->questiondesc}</td>
                <td><input name="section[{$section_rowid}][questions][{$question_rowid}][description]"  value="{$section[$section_rowid][questions][$question_rowid][description]}" type="text" size="40" autocomplete="on"></td>
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
                <td><input name="section[{$section_rowid}][questions][{$question_rowid}][commentsFieldTitle]" value="{$section[$section_rowid][questions][$question_rowid][commentsFieldTitle]}"  title="{$lang->commentsFieldTitle_tip}" type="text" size="40"></td>
            </tr>
            <tr>
                <td><img  src="{$core->settings['rootdir']}/images/icons/double arrow.png"> {$lang->commentsfieldsize}</td>
                <td><input accept="numeric" id="commentsFieldSize" value="{$section[$section_rowid][questions][$question_rowid][commentsFieldSize]}" name="section[{$section_rowid}][questions][{$question_rowid}][commentsFieldSize]" type="text" size="10" title="{$lang->commentsfieldsize_tip}" maxlength="3"><span id="fieldsizevalidate"></span></td>
            </tr>
            <tr id="section[{$section_rowid}][questions][{$question_rowid}][fieldSize_container]" {$style[fieldsizedisplay]}>
                <td>{$lang->fieldsize}</td>
                <td><input accept="numeric" name="section[{$section_rowid}][questions][{$question_rowid}][fieldSize]" value="{$section[$section_rowid][questions][$question_rowid][fieldSize]}" type="text" value="" size="5"></td>
            </tr>
            <tr id="section[{$section_rowid}][questions][{$question_rowid}][validationType_container]" {$style[hasvlidationdisplay]}>
                <td>{$lang->validationtype}</td>
                <td>
                    <select id="section_[{$section_rowid}]_[questions]_[{$question_rowid}]_[validationType]" name="section[{$section_rowid}][questions][{$question_rowid}][validationType]">
                        <option value="" selected='selected'>&nbsp;</option>
                        <option value="minchars" {$selectedvtype[type_minchars]}>{$lang->minchars}</option>
                        <option value="maxchars" {$selectedvtype[maxchars]}>{$lang->maxchars}</option>
                        <option value="email" {$selectedvtype[email]}>{$lang->emailonly}</option>
                        <option value="numeric" {$selectedvtype[numeric]}>{$lang->numbersonly}</option>
                    </select>
                </td>
            </tr>
            <tr id="section_[{$section_rowid}]_[questions]_[{$question_rowid}]_[validationCriterion]" {$style[validationCriterion]}>
                <td>{$lang->validationcriterion}</td>
                <td><input type="text" name="section[{$section_rowid}][questions][{$question_rowid}][validationCriterion]" value="{$section[$section_rowid][questions][$question_rowid][validationCriterion]}" accept="numeric"></td>
            </tr>


            <tr id="section[{$section_rowid}][questions][{$question_rowid}][matrixchoices_container]" {$style[matrixchoicesdisplay]}>
                <td colspan="2">
                    <table width="100%">
                        <thead>
                            <tr>
                                <td colspan="3"><hr /><span class="subtitle">{$lang->rows}</span></td>
                            </tr>
                            <tr>
                                <td id="answer_{$section_rowid}_{$question_rowid}" style="{$showanswer}">{$lang->isanswer}</td>
                                <td>{$lang->rows}*</td>
                            </tr>
                        </thead>
                        <tbody id="matrixquestionschoices_{$section_rowid}_{$question_rowid}_tbody"  class="{$altrow_class}">
                            {$matrixchoices}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">
                                    <img src="./images/add.gif" id="ajaxaddmore_surveys/createsurveytemplate_matrixquestionschoices_{$section_rowid}_{$question_rowid}" alt="{$lang->add}">
                                    <input id="numrows_matrixquestionschoices_{$section_rowid}_{$question_rowid}" name="numrows_questions_{$section_rowid}_{$question_rowid}" type="hidden" value="{$matrixchoicesrowid_rowid}">
                                    <input type="hidden" name="ajaxaddmoredata[type]" id="ajaxaddmoredata_matrixquestionschoices_{$section_rowid}_{$question_rowid}" value="{$type}"/>
                                    <input type="hidden" name="ajaxaddmoredata[questionrowid]" id="ajaxaddmoredata_matrixquestionschoices_{$section_rowid}_{$question_rowid}" value="{$question_rowid}"/>
                                    <input type="hidden" name="ajaxaddmoredata[sectionrowid]" id="ajaxaddmoredata_matrixquestionschoices_{$section_rowid}_{$question_rowid}" value="{$section_rowid}"/>

                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>

            <tr id="section[{$section_rowid}][questions][{$question_rowid}][choices_container]" {$style[choicesdisplay]}>
                <td colspan="2">
                    <table width="100%">
                        <thead>
                            <tr>
                                <td colspan="3"><hr /><span class="subtitle">{$lang->choices}</span></td>
                            </tr>
                            <tr>
                                <td >{$lang->choicesseperator}</td>
                                <td >{$seperatorselectlist}</td>
                            </tr>
                            <tr>
                                <td id="answer_{$section_rowid}_{$question_rowid}" style="{$showanswer}">{$lang->isanswer}</td>
                                <td>{$lang->choice}*</td>
                                <td>{$lang->value}</td>
                            </tr>
                        </thead>
                        <tbody id="questionschoices_{$section_rowid}_{$question_rowid}_tbody"  class="{$altrow_class}">
                            {$choices}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">
                                    <img src="./images/add.gif" id="ajaxaddmore_surveys/createsurveytemplate_questionschoices_{$section_rowid}_{$question_rowid}" alt="{$lang->add}"><input id="numrows_questionschoices_{$section_rowid}_{$question_rowid}" name="numrows_questions_{$section_rowid}_{$question_rowid}" type="hidden" value="{$choicesrowid_rowid}">
                                    <input type="hidden" name="ajaxaddmoredata[type]" id="ajaxaddmoredata_questionschoices_{$section_rowid}_{$question_rowid}" value="{$type}"/>
                                    <input type="hidden" name="ajaxaddmoredata[questionrowid]" id="ajaxaddmoredata_questionschoices_{$section_rowid}_{$question_rowid}" value="{$question_rowid}"/>
                                    <input type="hidden" name="ajaxaddmoredata[sectionrowid]" id="ajaxaddmoredata_questionschoices_{$section_rowid}_{$question_rowid}" value="{$section_rowid}"/>

                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>