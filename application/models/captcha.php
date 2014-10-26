<?php

class Captcha extends CI_Model {

	public function create () {

		$args = array (
		    'img_path'   => './public/captcha/',
		    'img_url'    => base_url ('public/captcha/') . '/',
		    'font_path'  => './public/fonts/capture_it.ttf',
		    'font_size'	 => 42,
		    'img_width'  => 400,
		    'img_height' => 80,
		    'expiration' => 3600
		);

		return create_captcha($args);
	}
}