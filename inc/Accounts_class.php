<?php

class Accounts {

    protected static function create_password($password, $salt) {
        $md5pass = md5($password);
        return md5(md5($salt) . $md5pass);
    }

    public static function generate_password_string($length) {
        $string = self::random_string($length);
        if (!self::validate_password_complexity($string)) {
            $string = self::generate_password_string($length);
        }
        return $string;
    }

    protected static function create_crypt_password($password, $salt, $workload = 12) {
        return crypt($password, "$2a$" . $workload . "$" . $salt);
    }

    protected static function create_salt() {
        if (function_exists('random_string')) {
            return random_string(8);
        }
        else {
            return self::random_string(8);
        }
    }

    protected static function create_advanced_salt() {
        return substr(str_replace('+', '.', base64_encode(sha1(microtime(true), true))), 0, 22);
    }

    protected static function create_loginkey() {
        if (function_exists('random_string')) {
            return random_string(40);
        }
        else {
            return self::random_string(40);
        }
    }

    protected static function username_exists($username) {
        global $db;

        $result = $db->fetch_array($db->query("SELECT COUNT(*) AS userscount FROM " . Tprefix . "users WHERE username='" . $db->escape_string($username) . "'"));
        if ($result['userscount'] > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function random_string($length, $simple = false) {
        $keys = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ012345679';
        if ($simple == false) {
            $keys .= '@#$%_^&*';
        }

        $max = strlen($keys) - 1;

        for ($i = 0; $i < $length; $i++) {
            $rand = rand(0, $max);
            $rand_key[] = $keys{$rand};
        }

        $output = implode('', $rand_key);
        return $output;
    }

    protected static function validate_password_complexity($data) {
//        $password_pattern = "/(?=^.{8,}$)(?=.*\d)(?=.*\W+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";
//        if(preg_match($password_pattern, $data)) {
//            return true;
//        }
//        else {
//            return false;
//        }
        return true;
    }

    protected static function in_passwordarhive($password, $uid) {
        if (value_exists('users_passwordarchive', 'password', md5($password), 'uid=' . intval($uid))) {
            return true;
        }
        return false;
    }

    public static function generate_employeenumber($affiliate) {
        $affiliate = new Affiliates($affiliate);
        $country_code = $affiliate->get_country()->get()['acronym'];
        if (empty($country_code)) {
            return false;
        }

        $random_num = substr(mt_rand(mt_rand(), mt_getrandmax()), 0, 4);

        $employeenum = $country_code . $random_num;
        if (value_exists('userhrinformation', 'employeeNum', $employeenum)) {
            $employeenum = Accounts::generate_employeenumber($affiliate);
        }
        return $employeenum;
    }

}

?>