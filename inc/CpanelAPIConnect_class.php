<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: cpanelapi_class.php
 * Created:        @zaher.reda    Sep 10, 2015 | 6:25:18 PM
 * Last Update:    @zaher.reda    Sep 10, 2015 | 6:25:18 PM
 */

/**
 * Description of cpanelapi_class
 *
 * @author zaher.reda
 */
include_once INC_ROOT.'xmlapi.php';

class CpanelAPIConnect {
    private $user = 'root';
    private $hash = '2ce0ebf53c2e2041920bdec8319efbd5
91c5c9d2cf255391a2fea2ef9b65b466
9e06719f6e4086bc65f333060b4839fb
5664b28a3f8e85ddb106eff3979efda5
2a8e9dca2a0020206184535e602c0d2c
970cbe256c7c4eb9eabbca11908ee3e9
7a36a1d59004caf5b421d65a9522aeb3
7f60faa9f4d420d3a03f3ba1080f0c26
c842e48f969c05aaca8d333b0d051377
08e5a1e1716708bf6edd7e362be0fe47
508c937f32e7a8b5b15842971936ae6f
2d99c3fee9c7cf052815134441628e82
aaba43ed04d5410918e6d063029950ee
03651005854ae1efe7df2912539ab1c0
741722f94741e232c9218193c533d377
bf4a2768946c2c1d97a686c76e7ba700
60b1e1c70e6d9c45ee318668d07ebfa3
02f1a09b9f2f122aa0da8c21f2d36832
6ae642caed6bd8fcbfca94f95fecf3e4
b48ee9cebaf31bc36467ae36b7be66e1
36e8294344f8a19fe74008d8571deb0d
5a5dbecdbdc7b259615b14b0436bb770
6205c7214ee5d8aaed712e41d0cd0bbd
17b00612db7423ca7a913a778c58e61a
3e18a7eff18ca8711bff3c2f9bd22242
6221e329ff601274c1b56d5088769bce
4fcd29c8e759374a7d274c0e4dfb12fd
e05c2960dc034d57af9c67eb20758604
0c265beb848c25434a0a54411603570e
2fc662b85f177c1fcf70b4b61f255b79';
    private $server = 'server.orkila.com';
    private $xmlapid;

    public function __construct() {
        $this->xmlapi = new xmlapi($this->server);
        $this->xmlapi->set_port(2087);
        $this->xmlapi->hash_auth($this->user, $this->hash);
    }

    public function get_xmlapi() {
        return $this->xmlapi;
    }

}