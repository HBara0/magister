<div>
    <div class="KPIGroupContainer">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-12 col-lg-12 component">
                        Approved
                    </div>
                    <div style="font-size: 16.968px;" class="col-xs-12 col-sm-6 col-md-12 col-lg-12 component component-border">
                        {$leaves_count[approved]}
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 BorderContainer">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-12 col-lg-12 component">
                        Pending
                    </div>
                    <div style="font-size: 16.968px;" class="col-xs-12 col-sm-6 col-md-12 col-lg-12 component component-border">
                        {$leaves_count[pending]}
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 BorderContainer ">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-12 col-lg-12 component">
                        Rejected
                    </div>
                    <div style="font-size: 16.968px;" class="col-xs-12 col-sm-6 col-md-12 col-lg-12 component component-border">
                        {$leaves_count[rejected]}
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>


<style>
    .KPIGroupContainer {
        text-align: center;
    }

    @media (max-width: 768px) {
        .component-border{
            border-left:none;
        }
    }
    @media (min-width: 768px) and (max-width:992px){
        .component-border{
            border-left:1px dotted #333;
        }
    }
    @media (max-width: 992px) {
        .component {
            font-size:13px !important;
            text-align: left !important;
        }
    }
    @media (min-width: 992px) {
        .BorderContainer {
            height: 70%;
            top: 15%;
            border-left: 1px dotted #333;
        }
    }
    @media (max-width: 1200px) {
        .component {
            font-size:15px !important;
        }
    }

</style>