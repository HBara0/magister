<h1>{$lang->reinitializebalances}</h1>
<form action="index.php?module=attendance/reinitializebalances&amp;action=selectusers" method="post">
    <div style="width:15%; display:inline-block; margin: 5px; font-weight:bold;">{$lang->affiliate}</div>
    <div style="width:80%; display:inline-block; margin: 5px;">{$affid_field}</div>
    <hr />
    <input type="submit" value="{$lang->next}" class="button">
</form>