<div class="container-fluid">
    <form name="perform_portal/conversation_Form" id="perform_portal/conversation_Form"  action="#" method="post">
        <input type="hidden"  name="conversation[tableName]" value="{$conv_data[tableName]}" />
        <input type="hidden"  name="conversation[recordId]" value="{$conv_data[recordId]}" />
        <input type="hidden"  name="conversation[module]" value="{$conv_data[module]}" />
        <input type="hidden"  name="conversation[alternativeId]" value="{$conv_data[alternativeId]}" />
        <input type="hidden"  name="conversation[inputChecksum]" value="{$inputChecksum}" />

        <div class="form-group">
            <label for="convtitle">{$lang->title}</label>
            <input type="text" class="form-control" id="convtitle" name="conversation[title]" placeholder="{$lang->title}" value='{$conv_data['title']}'>
        </div>
        <label for="participants">{$lang->participants}</label>
        <table id='participants'  class="datatable_basic table table-bordered row-border hover order-column" data-checkonclick='true' cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>{$lang->name}</th>
                    <th>{$lang->mainaffiliate}</th>
                    <th>{$lang->position}</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>&nbsp;</th>
                    <th>{$lang->name}</th>
                    <th>{$lang->mainaffiliate}</th>
                    <th>{$lang->position}</th>
                </tr>
            </tfoot>
            <tbody>
                {$participants_list}
            </tbody>
        </table>
        <label for="message">{$lang->writeyourmsghere}</label>
        <div id="message" style="display:block; padding: 8px;" class="hidden-print">
            <textarea id="messagetext" class="txteditadv" cols="40" rows="5" name="message[message]" placeholder='{$lang->writeyourmsghere}'></textarea>
            <input type="hidden"  name="message[inputChecksum]" value="{$message_inputChecksum}" />
        </div>
        <input type="submit" id="perform_portal/conversation_Button" value="{$lang->submit}" class="button"/>
    </form>
    <div id="perform_portal/conversation_Results"></div>
</div>