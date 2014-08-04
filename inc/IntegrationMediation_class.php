<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: IntegrationMediation_class.php
 * Created:        @zaher.reda    Jul 17, 2014 | 10:28:44 AM
 * Last Update:    @zaher.reda    Jul 17, 2014 | 10:28:44 AM
 */

/**
 * Description of IntegrationMediation_class
 *
 * @author zaher.reda
 */
class IntegrationMediation {
    protected $foreign_system;
    protected $affids;
    private $status = 0;

    public function __construct($affids, $foreign_system) {
        if(!empty($foreign_system)) {
            $this->set_foreign_system($foreign_system);
        }
        else {
            $this->status = 100601;
            return false;
        }
    }

    protected function set_affiliates(array $affids) {
        $this->affiliates = $affids;
    }

    private function set_foreign_system($foreign_system) {
        $this->foreign_system = intval($foreign_system);
    }

}