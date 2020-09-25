<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/********************************************************
             ______@25/07/2020______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /_ _ /
********************************************************/
class JDSignature extends MY_Controller {
	function index(){
		$html = "<script src='".base_url('public/js/SYS06/JD_Signature.js')."'></script>";
		echo $html;
	}	
}




















