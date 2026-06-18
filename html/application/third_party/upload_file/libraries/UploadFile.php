<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadFile extends CI_Controller {
        private $ci;
        public function __construct()
        {
                $this->ci =   & get_instance();
                $this->ci->load->helper('url');
                $this->ci->load->config('upload');
                // $this->load->helper('url');
                // $this->load->config('upload');
                //$this->load->library('session');
                //parent::__construct();
        }
        
        public function subirArchivo($file,$filename,$UploadFile="")
        {

                $config['file_name']            = $filename;
                if($UploadFile != ""){
                        $config['upload_path']          = $UploadFile;
                } else {
                        $config['upload_path']          = $this->ci->config->item('upload_path');
                }
                $config['allowed_types']        = $this->ci->config->item('allowed_types');
                $config['max_size']             = $this->ci->config->item('max_size');
                $config['max_width']            = $this->ci->config->item('max_width');
                $config['max_height']           = $this->ci->config->item('max_height');

                $this->ci->load->library('upload', $config);

                if ( ! $this->ci->upload->do_upload($file)) {
                        $info['response'] = false;
                        $info['info'] = $this->ci->upload->display_errors();
                        return $info;
                }
                else {
                        $info['response'] = true;
                        $info['info'] = $this->ci->upload->data();
                        return $info;
                }
        }
}
?>