<?php
/*
  - * Copyright © 2015 Orkila International Offshore, All Rights Reserved
  - *
  - * [Provide Short Descption Here]
  - * $id: ReportContributors_class.php
  - * Created:        @zaher.reda    May 27, 2015 | 11:08:09 AM
  - * Last Update:    @zaher.reda    May 27, 2015 | 11:08:09 AM
  - */

/**
  - * Class for QR contributors
  - *
  - * @author zaher.reda
  - */
class ReportContributors extends AbstractClass {
    const PRIMARY_KEY = 'rcid';
    const TABLE_NAME = 'reportcontributors';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'rid,uid';

    protected function update(array $data) {

    }

    /**
     *
     * @return \ReportingQr
     */
    public function get_report() {
        return new ReportingQr(array('rid' => $this->data['rid']));
    }

    /**
     *
     * @return \Users
     */
    public function get_user() {
        return new Users($this->data['uid']);
    }

}