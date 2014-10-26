<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{

		$this->load->model ('captcha');
		$captcha = $this->captcha->create ();

		$this->session->set_userdata (array ('captcha' => $captcha['word']));

		$stats = array (
			'actors'	=> $this->stats->countActors (),
			'movies'	=> $this->stats->countMovies (),
			'links'		=> $this->stats->countLinks (),
		);

		$this->load->view ('main', array (
			'captcha'	=> $captcha['image'],
			'stats'		=> $stats,
			)
		);
	}

}
