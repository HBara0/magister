{$leave_details}
<br/>
<h3  style="font-size: 24px;color: #91B64F;font-weight: 100;">{$lang->viewplan} : {$plan_name}</h3>
<!--<a  style="font: bold 11px Arial;
    text-decoration: none;
    background-color: #EEEEEE;
    color: #333333;
    padding: 2px 6px 2px 6px;
    border-top: 1px solid #CCCCCC;
    border-right: 1px solid #333333;
    border-bottom: 1px solid #333333;
    border-left: 1px solid #CCCCCC;" href="{$approve_link}" target="_blank">{$lang->commentapprovereject}</a></br>
-->
<div id="container" style="width:100%;  margin: 0px auto;display:block;">
    </br>
    <div style=" width:100%; background-color: lightgray; display: block;">{$segment_expenses} </div>
    {$segment_details}
    <div style="font-size: 24px;color: #91B64F;font-weight: 100;">{$transportaion_fields_title} </div>
    {$transportaion_fields}
</div>
