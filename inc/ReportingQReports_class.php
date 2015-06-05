
<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: ReportingQReports_class.php
 * Created:        @zaher.reda    May 27, 2015 | 6:03:37 PM
 * Last Update:    @zaher.reda    May 27, 2015 | 6:03:37 PM
 */

/**
 * Description of ReportingQReports_class
 *
 * @author zaher.reda
 */
class ReportingQReports extends AbstractClass {
    const PRIMARY_KEY = 'rid';
    const TABLE_NAME = 'reports';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;

    protected function update(array $data) {

    }

    /**
     *
     * @return \Affiliates
     */
    public function get_affiliate() {
        return new Affiliates($this->affid);
    }

}