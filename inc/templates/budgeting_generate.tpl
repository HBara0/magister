<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillsurvey}</title>
        {$headerinc}
        <script type="text/javascript">
            $(document).ready(function() {
                $('input[name=fxrate]').live('change', function() {
                    $('#fxrateinput').slideToggle('fast', function() {
                    });
                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->generatebudget}</h1>
            <form name="perform_budgeting/generate_Form" id="perform_budgeting/generate_Form" method="post" action="index.php?module=budgeting/preview&amp;referrer=generate">
                <input type="hidden" name="identifier" value="{$core->input[identifier]}"/>
                <div style="display:block; padding:8px;">
                    <table>
                        <div class="thead" >{$lang->filterby}</div>

                        <tr>
                            <td  width="50%">
                                <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                    <table class="datatable" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="100%">{$lang->affiliate}<input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->affiliate}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                            {$affiliates_list}
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                            <td  width="50%">
                                <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                    <table class="datatable" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="100%">{$lang->supplier}<input class='inlinefilterfield' type='text' tabindex="2" placeholder="{$lang->search} {$lang->supplier}" style="width:70%;display:inline-block;margin-left:5px;"/></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {$suppliers_list}
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <td  width="50%">
                            <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                <table class="datatable" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="100%">{$lang->bm}<input class='inlinefilterfield' type='text' placeholder="{$lang->search} {$lang->bm}" style="width:60%;display:inline-block;margin-left:5px;"/></th>
                                        </tr>
                                    </thead>
                                    <tbody style="height:100px;">
                                        {$business_managerslist}
                                    </tbody>
                                </table>
                            </div>
                        </td>
                        <td  width="50%">
                            <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                <table class="datatable" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="100%">{$lang->segment}<input class='inlinefilterfield' type='text' placeholder="{$lang->search} {$lang->segment}" style="width:70%;display:inline-block;margin-left:5px;"/></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$budget_segments_list}
                                    </tbody>
                                </table>
                            </div>
                        </td>
                        </tr>
                        <td  width="50%">
                            <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                <table class="datatable" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="100%">{$lang->year}<input class='inlinefilterfield' type='text' placeholder="{$lang->search} {$lang->year}" style="width:70%;display:inline-block;margin-left:5px;"/></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$budget_year_list}
                                    </tbody>
                                </table>
                            </div>
                        <td>
                        <td  width="50%">

                        </td>
                        </tr>
                    </table>
                    <!-- <div style="display:block; padding:15px;">
                         <div style="display:inline-block;padding:10px;">{$lang->curr}</div>
                         <div style="display:inline-block;padding:10px;"></div>
                     </div> -->
                    <!--<div id="budget_currautomatic" style="display:block;">
                       <div style="display:inline-block;padding:8px; margin-left:65px;"><input  id="autofxrate"  type="radio" value=1 checked name="fxrate"/></div>
                       <div style="display:inline-block;padding:8px;">{$lang->automaticusdrate}</div>
                   </div>
                  <div  id="budget_currspecify"style="display:block;">
                       <div style="display:inline-block;padding:8px; margin-left:65px;"><input  id="specifyfxrate" type="radio" value=2 name="fxrate"/></div>
                       <div style="display:inline-block;padding:8px;"> {$lang->specifyusdrate}<span id="fxrateinput" style="display:none;"><input type="texr" size="8" name="budgetgenerate[fxrate]"/></span>

                       </div>-->
                </div>
                <div  id="budget_currspecify"style="display:block;">
                    <input type="submit" value="{$lang->generate}" class="button"> <input type="reset" value="{$lang->reset}" class="button">
                </div>
            </form>
        </td>
    </tr>
</body>
</html>