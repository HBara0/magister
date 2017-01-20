<div class="row">
    <div class="col-md-9 col-lg-9 col-sm-12">
        <h1>{$lang->managerecommendation}</h1>
    </div>
</div>
<form  action="#" method="post" id="perform_travel/managerecommendation_Form" name="perform_travel/managerecommendation_Form">
    <input type="hidden" name="recommendation[rid]" value="{$recommendation[rid]}">
    <div class="form-group-lg">
        <label for="title" style="font-weight: bold">{$lang->title}<span style="color:red"> *</span></label>
        <input required="required" name='recommendation[title]' value='{$recommendation[title]}' type="text" class="form-control" id="code" placeholder="{$lang->title}">
    </div>
    <label>{$lang->city}<span style="color:red"> *</span></label>
    <div class="form-group-lg">
        <input class="form-control" type='text' id="cities_1_autocomplete" name="cities_name" value="{$recommendation[city_output]}" autocomplete='off'/>
        <input type='hidden' size='2' id='cities_1_id_output' disabled='disabled' />
        <input type='hidden' id='cities_1_id' name='recommendation[city]' value="{$recommendation[city]}" />
        <div id='searchQuickResults_cities_1' class='searchQuickResults' style='display:none;'></div>
    </div>
    <label>{$lang->category}<span style="color:red"> *</span></label>
    <div class="form-group-lg">
        {$categories_list}
    </div>
    <label>{$lang->rating}</label>
    <div class="form-group-lg">
        {$rating_list}
    </div>

    <div class="form-group-lg">
        <label for="description">{$lang->description}</label>
        <div style="display:block;">
            <textarea name="recommendation[description]" cols="100" rows="6" id='description' class="basictxteditadv">
                {$recommendation[description]}
            </textarea>
        </div>
    </div>
    <label>{$lang->isActive}</label>
    <div class="form-group-lg">
        {$isactive_list}
    </div>
    <input type="submit" value="{$lang->savecaps}" id="perform_travel/managerecommendation_Button" class="button"/>
    <div id="perform_travel/managerecommendation_Results"></div>

</form>
