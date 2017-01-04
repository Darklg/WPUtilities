<?php

/*
Plugin Name: WPU Check IP
Description: Check IP use frequency
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

/*
$wpu_check_ip = new wpu_check_ip();
$check = $wpu_check_ip->ip_is_clean('contact_form');
*/

class wpu_check_ip {
    private $opts = array(
        'duration_check' => 600,
        'number_check' => 10,
        'number_conserve' => 20
    );

    public function __construct($opts = array()) {
        $this->opts = $this->check_opts($opts);
    }

    public function ip_is_clean($tag = 'default', $opts = array()) {
        $ip = $this->get_client_ip();
        /* Get options */
        $this->opts = $this->check_opts($opts);
        /* Get list */
        $list = $this->get_list($tag);
        /* Add IP to list */
        $list = $this->add_ip($ip, $list);
        /* Clean list */
        $list = $this->clean_list($list);
        /* Save list */
        $this->save_list($tag, $list);
        /* Check if item is present N times ( N = number_check ) */
        $i = 0;
        foreach ($list as $item) {
            if ($item['ip'] == $ip) {
                $i++;
            }
        }
        return ($i < $this->opts['number_check']);
    }

    /* Get IP : http://stackoverflow.com/a/15699240 */
    public function get_client_ip() {
        $ip_conf = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        foreach ($ip_conf as $conf) {
            if (isset($_SERVER[$conf])) {
                return $_SERVER[$conf];
            }
        }
        return 'UNKNOWN';
    }

    /* Add item */
    public function add_ip($ip, $list) {
        $list[] = array(
            'ip' => $ip,
            'time' => time()
        );
        return $list;
    }

    /* Save list */
    public function save_list($tag, $list) {
        return set_transient('wpu_check_ip__' . $tag, $list, $this->opts['duration_check']);
    }

    /* Clean list */
    public function clean_list($list) {
        $time = time();
        /* Keep only N last items ( N = number_conserve ) */
        $list = array_slice($list, 0 - $this->opts['number_conserve'], $this->opts['number_conserve']);
        /* Delete elements after duration check */
        $r_list = array();
        foreach ($list as $k => $val) {
            if ($this->opts['duration_check'] + $val['time'] >= $time) {
                $r_list[] = $val;
            }
        }
        return $r_list;
    }

    /* Get list */
    public function get_list($tag) {
        $list = get_transient('wpu_check_ip__' . $tag);
        if (!is_array($list)) {
            $list = array();
        }
        return $list;
    }

    /* Check opts */
    public function check_opts($opts) {
        if (!is_array($opts)) {
            $opts = array();
        }
        foreach ($this->opts as $k => $opt) {
            if (!isset($opts[$k])) {
                $opts[$k] = $this->opts[$k];
            }
        }
        return $opts;
    }
}
