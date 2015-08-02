<?php
// Welcome to the RazorFlow PHP Dashbord Quickstart. Simply copy this "dashboard_quickstart"
// to somewhere in your PHP server to have a dashboard ready to use.
// This is a great way to get started with RazorFlow with minimal time in setup.
// However, once you're ready to go into deployment consult our documentation on tips for how to
// maintain the most stable and secure
// Require the library file
require "razorflow_php/razorflow.php";

class SampleDashboard extends StandaloneDashboard {
    public function buildDashboard() {
        $c1 = new KPIComponent('kpi1');
        $c1->setDimensions(3, 2);
        $c1->setCaption('KPI 1');
        $c1->setValue(42);

        $this->addComponent($c1);

        $c2 = new KPIComponent('kpi2');
        $c2->setDimensions(3, 2);
        $c2->setCaption('KPI 2');
        $c2->setValue(43);
        $this->addComponent($c2);

        $c3 = new KPIComponent('kpi3');
        $c3->setDimensions(3, 2);
        $c3->setCaption('KPI 3');
        $c3->setValue(44);
        $this->addComponent($c3);

        $c1->overrideDisplayOrderIndex(2);
        $c2->overrideDisplayOrderIndex(1);
        $c3->overrideDisplayOrderIndex(0);
    }

}
$db = new SampleDashboard();


// Here, we're manually setting the static root to where the CSS and HTML is available.
// This is relative to the current path of index.php and will not work in more advanced
// scenarios like integrating into MVC and embedding.
$db->setStaticRoot("razorflow_php/static/rf/");
$db->enableDevTools();
$db->renderStandalone();

