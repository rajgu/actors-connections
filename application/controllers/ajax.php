<?php

class Ajax extends CI_Controller {
	
	public function index () {
	}


	public function contact () {

		$name    = $this->input->post ('name');
		$email   = $this->input->post ('email');
		$message = $this->input->post ('message');
		$captcha = $this->input->post ('captcha');
		$code    = $this->session->userdata ('captcha');

		$this->load->model ('captcha');
		$new_captcha = $this->captcha->create ();
		$this->session->set_userdata (array ('captcha' => $new_captcha['word']));

		$errors = array ();
		if (
			! $captcha OR
			($captcha AND strtolower ($captcha) != strtolower ($code))
		) {
			$errors[] = 'Please, enter code as on image';
		}
		if ( ! $name) {
			$errors[] = 'Please, enter your name';
		}
		if ( ! $message) {
			$errors[] = 'So.... U dont have anything to say to me.... <br /> Why bother than?';
		}

		if (empty ($errors)) {
			$this->load->helper ('email');
			$status = send_email (
				'rajgu85@gmail.com',
				'[AC] - From "' . $name . '"',
				'Email: "' . $email . "\"\n\n" . $message
			);

			if ( ! $status) {
				$errors[] = "I'm very sorry, there's something wrong with all those emails...";
			} else {
				$output = array (
					'status'	=> 'ok',
					'text'		=> "Thanks for sharing Your fought's",
					'captcha'	=> $new_captcha['image'],
				);
				$this->output
				    ->set_content_type ('application/json')
				    ->set_output (json_encode ($output));
				return;
			}
		}

		$output = array (
			'status'	=> 'error',
			'text'		=> $errors,
			'captcha'	=> $new_captcha['image'],
		);
		$this->output
		    ->set_content_type ('application/json')
		    ->set_output (json_encode ($output));
	}


	public function search () {
		$search = $this->input->post ('search');
		$search = explode (' ', trim ($search));

		$actors = $this->actors->searchByName ($search);

		if (! empty ($actors)) {


		}
		$actors = array_map (function ($actor) { return $actor['name'];}, $actors);

		$this->output
		    ->set_content_type ('application/json')
		    ->set_output (json_encode ($actors));
	}


	public function validate () {

		$actor1 = $this->input->post ('actor1');
		$actor2 = $this->input->post ('actor2');

		$actor1 = $this->actors->getByName ($actor1);
		$actor2 = $this->actors->getByName ($actor2);

		if (! $actor1 OR ! $actor2) {
			$output = array (
				'status'	=> 'error',
				'text'		=> 'Hmm.. There\'s something wrong here...',
			);
			$this->output
			    ->set_content_type ('application/json')
			    ->set_output (json_encode ($output));
			return;
		}

		if ($actor1['id'] == $actor2['id']) {
			$output = array (
				'status'	=> 'error',
				'text'		=> 'I know what u did here :)',
			);
			$this->output
			    ->set_content_type ('application/json')
			    ->set_output (json_encode ($output));
			return;
		}

		$this->stats->incStat ('queries');
		$cache  = $this->cache->getCache (array ('actor1' => $actor1['id'], 'actor2' => $actor2['id']));

		if ($cache) {
			$this->stats->incStat ('cached');
			$output = array (
				'status'	=> 'ok',
				'text'		=> $cache,
			);
			$this->output
			    ->set_content_type ('application/json')
			    ->set_output (json_encode ($output));
			return;
		}

		$connection = $this->a2m->getActorsConnection ($actor1['id'], $actor2['id']);

		if ( ! $connection) {
			$output = array (
				'status'	=> 'fail',
				'text'		=> "I have been searching... but I haven't found it.",
			);
			$this->output
			    ->set_content_type ('application/json')
			    ->set_output (json_encode ($output));
			return;
		}

		$result = $this->a2m->transformResults ($connection, $actor1, $actor2);

		if ( ! $result) {
			$output = array (
				'status'	=> 'error',
				'text'		=> 'An internal error occurred, please try again later. If problems persist contact us',
			);
			$this->output
			    ->set_content_type ('application/json')
			    ->set_output (json_encode ($output));
			return;			
		}

		$this->cache->saveCache (array (
			'actor1'	=> $actor1,
			'actor2'	=> $actor2,
			'data'		=> $result,
		));

		$this->stats->incStat ('found');

		$output = array (
			'status'	=> 'ok',
			'text'		=> $result,
		);
		$this->output
		    ->set_content_type ('application/json')
		    ->set_output (json_encode ($output));

	}


	public function statistics () {

		$img = imagecreatefrompng ('public/imgs/stats.png');
		imagesavealpha ($img, true);

		$colors = array (
			'red'			=> imagecolorallocate ($img, 153, 0, 0),
			'green'			=> imagecolorallocate ($img, 0, 153, 0),
			'blue'			=> imagecolorallocate ($img, 0, 0, 153),
			'red_border'	=> imagecolorallocate ($img, 0, 0, 0),
			'green_border'	=> imagecolorallocate ($img, 0, 0, 0),
			'blue_border'	=> imagecolorallocate ($img, 0, 0, 0),
			'black'			=> imagecolorallocate ($img, 0, 0, 0),
		);

		$font = './public/fonts/NanumGothicBold.ttf';
		$stats = $this->stats->getStats ();
		$run = 0;
		$height = 370;
		foreach ($stats AS $stat) {
			$height_queries = $height;
			imagefilledrectangle ($img, 50 + (280 * $run), 450, 120 + (280 * $run), 80 + $height - $height_queries, $colors['red']);
			imagerectangle ($img, 50 + (280 * $run), 450, 120 + (280 * $run), 80 + $height - $height_queries, $colors['red_border']);

			if (isset ($stat['queries']) AND $stat['queries'] > 0) {
				$percent = ($stat['cached'] * 100 / $stat['queries']);
				$height_cached = floor ($percent * $height / 100);
			} else {
				$height_cached = 0;
			}

			imagefilledrectangle ($img, 130 + (280 * $run), 450, 200 + (280 * $run), 80 + $height - $height_cached, $colors['green']);
			imagerectangle ($img, 130 + (280 * $run), 450, 200 + (280 * $run), 80 + $height - $height_cached, $colors['green_border']);

			if (isset ($stat['queries']) AND $stat['queries'] > 0) {
				$percent = ($stat['found'] * 100 / $stat['queries']);
				$height_found = floor ($percent * $height / 100);
			} else {
				$height_found = 0;
			}

			imagefilledrectangle ($img, 210 + (280 * $run), 450, 280 + (280 * $run), 80 + $height - $height_found, $colors['blue']);
			imagerectangle ($img, 210 + (280 * $run), 450, 280 + (280 * $run), 80 + $height - $height_found, $colors['blue_border']);

			imagettftext ($img, 22, 0,  50 + 35 - 4 - ((strlen ($stat['queries']) - 1)  * 11) + ($run * 280), 450 - 2 - $height_queries, $colors['black'], $font, $stat['queries']);
			imagettftext ($img, 22, 0, 130 + 35 - 1 - ((strlen ($stat['queries']) - 1)  * 11) + ($run * 280), 450 - 2 - $height_cached, $colors['black'], $font, $stat['cached']);
			imagettftext ($img, 22, 0, 210 + 35 + 2 - ((strlen ($stat['queries']) - 1)  * 11) + ($run * 280), 450 - 2 - $height_found, $colors['black'], $font, $stat['found']);

			$run++;
		}
		imagettftext ($img, 22, 0, 124, 480, $colors['black'], $font, "Today");
		imagettftext ($img, 22, 0, 370, 480, $colors['black'], $font, "Yesterday");
		imagettftext ($img, 22, 0, 650, 480, $colors['black'], $font, "Last Week");

		header ('Content-Type: image/png');
		imagepng ($img);
	}

}
