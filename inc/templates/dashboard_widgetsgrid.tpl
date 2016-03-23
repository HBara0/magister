<style>
    #dashboard_{$dashid}_list { list-style-type: none; margin: 0; padding: 0;width: 100%}
    #dashboard_{$dashid}_list li{ margin: 10px 10px 10px 0; padding: 5px;float: left; width: 40%;}
</style>
<script>
    $(function() {
        $("ul[id^='dashboard_'][id$='_list']").sortable({placeholder: "ui-state-highlight", forcePlaceholderSize: true, delay: 300, opacity: 0.5, handle: '.widgets-sort-icon'});
    });
</script>
<div style="float:right;width:80%;height:100%">
    <ul id="dashboard_{$dashid}_list">
        {$widgets_output}
    </ul>
</div>
