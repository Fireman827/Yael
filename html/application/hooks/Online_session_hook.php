<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Online_session_hook {

    public function detectar()
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        if (preg_match('#/(pedidos|Online)(/|$)#i', $uri)) {
            define('ONLINE_SESSION', TRUE);
        }
    }
}
