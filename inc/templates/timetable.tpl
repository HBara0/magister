<script>
    $(function() {
        $(document).ready(SetIframeSize);

// resize on window resize
        $(window).on('resize', SetIframeSize);

        function SetIframeSize() {
            $("#external").width($(window).width()); // added margin for scrollbars
            $("#external").height($(window).height() - 35);
        }

    })
</script>

<div style="width:40%; display:inline-block;"><h1>{$lang->timetable}</h1></div>
<div>
    <button type="button" class="btn btn-success" onclick="window.open('{$timetablelink}', '_blank')">Open in Google Drive</button>
    <br/>
    <div id="iframe-wrapper" align="center" style="z-index: 0">
        <iframe id="external" src="https://docs.google.com/a/edu.esiee.fr/spreadsheets/d/1wjDsYACh2xeBvbarftwx-ne_izs1uPFvhT8Ko4qn_Rg/pubhtml?widget=true&amp;headers=false"></iframe>
    </div>
</div>