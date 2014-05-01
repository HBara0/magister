<?php

class Imageverification {
    protected $verification_keyword;

    public function __construct() {
        echo $this->generate_imageverification();
    }

    public function generate_imageverification() {
        if(function_exists("random_string")) {
            $this->verification_keyword = random_string(4);
        }
        else {
            $this->verification_keyword = self::random_string(4);
        }
        return "<input type='text' id='image_verification' name=id='image_verification' /> <input type='text' value='".$this->verification_keyword."' size='4' disabled />";
    }

    protected function create_image() {
        $image = imagecreate(150, 30);
        $background_color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $line = imagecolorallocate($image, 233, 239, 239);
        $line2 = imagecolorallocate($image, 153, 165, 123);
        imageline($image, 10, 1, 30, 25, $line2);
        imageline($image, 1, 60, 40, 10, $line2);
        imageline($image, 5, 8, 20, 20, $line2);
        imageline($image, 6, 40, 20, 15, $line2);
        imagestring($image, 5, rand(18, 25), rand(2, 11), $this->verification_keyword, $text_color);

        header("Content-type: image/jpg");
        imagejpeg($image);
        imagedestroy($image);
    }

    public function verify_keyword($keyword) {
        if($keyword === $this->verification_keyword) {
            return true;
        }
        else {
            return false;
        }
    }

    public function get_verification_keyword() {
        return $this->verification_keyword;
    }

    protected static function random_string($length) {
        $keys = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = strlen($keys) - 1;

        for($i = 0; $i < $length; $i++) {
            $rand = rand(0, $max);
            $rand_key[] = $keys{$rand};
        }

        $output = implode('', $rand_key);
        return $output;
    }

}
?>