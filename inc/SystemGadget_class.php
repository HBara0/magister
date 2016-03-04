
<?php
/*
 * Copyright Â© 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: gadget_class.php
 * Created:        @zaher.reda    Feb 11, 2016 | 10:35:55 PM
 * Last Update:    @zaher.reda    Feb 11, 2016 | 10:35:55 PM
 */

/**
 * Description of gadget_class
 *
 * @author zaher.reda
 */
Abstract class SystemGadget extends AbstractClass {
    protected $data = array();

    const WIDGET_ID = '';

    public function __construct() {

    }

    //main abstract class functions - START
    public function create(array $data) {

    }

    public function update(array $data) {

    }

    //main abstract class functions - END
    /**
     * get system widget related to this instance of gadget through the
     * classname variable in the widget row
     * @return \SystemWidgets|boolean
     */
    public function get_systemwidget() {
        $widget = SystemWidgets::get_data(array('className' => self::CLASSNAME), array('returnarray' => false));
        if(is_object($widget)) {
            return $widget;
        }
        if(self::WIDGET_ID) {
            $widget = new SystemWidgets(intval(self::WIDGET_ID));
            if(is_object($widget) && !empty($widget->{SystemWidgets::PRIMARY_KEY})) {
                return $widget;
            }
            return false;
        }
    }

    abstract protected function feedobject();
    abstract public function parse();
}