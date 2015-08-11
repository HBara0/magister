<?php
/* -------Definiton-START-------- */

class SegmentCategories extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'scid';
    const TABLE_NAME = 'segmentscategories';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'name';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'title';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'title' => $data['title'],
                'name' => $data['name'],
                'description' => $data['description'],
                'shortDescription' => $data['shortDescription'],
                'publishOnWebsite' => $data['publishOnWebsite'],
                'largeBanner' => $data['largeBanner'],
                'mediumBanner' => $data['mediumBanner'],
                'smallBanner' => $data['smallBanner'],
                'slogan' => $data['slogan'],
                'brandingColor' => $data['brandingColor'],
                'featuredSequence' => $data['featuredSequence'],
                'includeInWebsiteCarousel' => $data['includeInWebsiteCarousel'],
        );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $update_array['title'] = $data['title'];
            $update_array['name'] = $data['name'];
            $update_array['description'] = $data['description'];
            $update_array['shortDescription'] = $data['shortDescription'];
            $update_array['publishOnWebsite'] = $data['publishOnWebsite'];
            $update_array['largeBanner'] = $data['largeBanner'];
            $update_array['mediumBanner'] = $data['mediumBanner'];
            $update_array['smallBanner'] = $data['smallBanner'];
            $update_array['slogan'] = $data['slogan'];
            $update_array['brandingColor'] = $data['brandingColor'];
            $update_array['featuredSequence'] = $data['featuredSequence'];
            $update_array['includeInWebsiteCarousel'] = $data['includeInWebsiteCarousel'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
}