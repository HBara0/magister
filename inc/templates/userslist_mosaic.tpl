<!-- {$lang->employeeslist}-->

<style type="text/css" media="all">
    .mosaicpiece {
        width: 100px;
        height: 90px;
        display: inline-block;
        position:relative;
        margin: 1px;
        z-index:auto;
    }

    .mosaicpiece img {
        position: absolute;
        width: 100%;
        height: 100%;
        vertical-align: top;
        z-index: 1;
        -webkit-transition: all 0.2s ease;
        -moz-transition:    all 0.2s ease;
        -o-transition:      all 0.2s ease;
        -ms-transition:     all 0.2s ease;
        transition:         all 0.2s ease;
    }

    .mosaicpiece img:hover {

        transform: scale(1.2);
        -ms-transform: scale(1.2); /* IE 9 */
        -webkit-transform: scale(1.2); /* Safari and Chrome */
        box-shadow: 2px 2px 4px rgb(204, 204, 204);
        z-index:2;
    }
</style>

<!--  <ul id="mainmenu">
      <li><span><a href="users.php?action=profile">{$lang->viewyourprofile}</a></span></li>
      <li><span><a href="users.php?action=profile&amp;do=edit">{$lang->manageyouraccount}</a></span></li>
  </ul>-->

<div style="float:right; text-align:center;"><a href="{$change_view_url}"><img src="./images/icons/{$change_view_icon}" alt="{$lang->changeview}" border="0"/></a></div>
<div id="mosaicgrid">
    {$userslistmosaic_pieces}
</div>
