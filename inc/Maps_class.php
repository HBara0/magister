<?php

class Maps {

    private $api_key = 'AIzaSyDXUgYSlAux8xlE8mA38T0-_HviEPiM5dU';
    private $places = array();
    private $options = array();

    public function __construct(array $places = array(), array $options = array()) {
        global $headerinc, $template;

        $this->places = $places;
        $this->options = $options;

        if (!isset($this->options['canvas_name'])) {
            $this->options['canvas_name'] = 'map_canvas';
        }

        if (!isset($this->options['mapcenter'])) {
            $this->options['mapcenter'] = '5.362467, 50.039063';
        }

        if (!empty($places)) {
            $places_script .= $this->parse_markers();
        }

        if (empty($this->options['overlaytype'])) {
            $this->options['overlaytype'] = 'parseMarkers';
        }

        if (empty($this->options['zoom'])) {
            $this->options['zoom'] = 2;
        }

        $places_script .= 'mapInitialize(' . $this->options['overlaytype'] . ');';
        eval("\$headerinc .= \"" . $template->get('headerinc_mapsapi') . "\";");
    }

    private function parse_markers() {
        if (is_array($this->places)) {
            foreach ($this->places as $id => $place) {
                $i = 1;
                $level2_comma = '';

                if (count($place, COUNT_RECURSIVE) != count($place)) {
                    $markers .= $level1_comma . '{"' . $id . '":[';
                    foreach ($place as $subid => $place_crumbs) {
                        list($latitude, $longitude) = explode(',', $place_crumbs['geoLocation']);
                        $markers .= $level2_comma . '{"id":"' . $subid . '", "title":"' . $place_crumbs['title'] . '","otherinfo":"' . $place_crumbs['otherinfo'] . '","lat":' . $latitude . ',"lng":' . $longitude . ',"link":"' . $this->parse_link($place_crumbs['type'], $id) . '","hasInfoWindow":1}' . "\n";
                        $level2_comma = ',';
                        $i++;
                    }
                    $level1_comma = ',';
                    $markers .= ']}' . "\n";
                }
                else {
                    list($latitude, $longitude) = explode(',', $place['geoLocation']);
                    $markers .= $level1_comma . '{"' . $id . '":{"id":"' . $id . '", "title":"' . $place['title'] . '","otherinfo":"' . $place['otherinfo'] . '","lat":' . $latitude . ',"lng":' . $longitude . ',"link":"' . $this->parse_link($place['type'], $id) . '","hasInfoWindow":1}}' . "\n";
                    ;
                    $level1_comma = ',';
                }
            }
            return 'places=[' . $markers . '];' . "\n";
        }
        return false;
    }

    private function parse_link($type, $id) {
        switch ($type) {
            case 'affiliateprofile':
                return 'index.php?module=profiles/affiliateprofile&affid=' . $id;
                break;
            default: return false;
        }
    }

    public function get_map($width = 600, $height = 400) {
        return '<div id="' . $this->options['canvas_name'] . '" style="height: ' . $height . 'px; width: ' . $width . 'px;"></div>';
    }

    public function reverse_geocoding($latitude, $longitude) {
        return json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $latitude . ',' . $longitude . '&sensor=false'));
    }

    public function get_streetname($latitude, $longitude, $parsedlocation = '') {
        if (empty($parsedlocation)) {
            return Maps::reverse_geocoding($latitude, $longitude)->results[0]->formatted_address;
        }
        else {
            return $parsedlocation->results[0]->formatted_address;
        }
    }

}

?>