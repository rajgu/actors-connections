<?php

class Stats extends CI_Model {
	
	public function Stats () {
	}


	public function countActors () {
		return $this->_count ('actors');
	}


	public function countMovies () {
		return $this->_count ('movies');
	}


	public function countLinks () {
		return $this->_count ('a2m');
	}


	private function _count ($tb_name) {
		$query = "SELECT count(*) AS ilosc FROM `$tb_name`;";
		$result = $this->db->query ($query);
		$data = $result->result_array ();
		if ($data AND isset ($data[0]['ilosc']))
			return $data[0]['ilosc'];
		return FALSE;
	}


	public function incStat ($stat_name) {
		$today = date ('Y-m-d', time ());
		$query = "SELECT `day` FROM `stats` WHERE `day` = '$today'";
		$result = $this->db->query ($query);
		$data = $result->result_array ();
		if (empty ($data))
			$result = $this->db->insert ('stats', array ('day' => $today));
		$this->db->simple_query ("UPDATE `stats` SET `$stat_name` = `$stat_name` + 1 WHERE `day` = '$today'");
	}


	public function getStats () {
		$stats = array (
			'today'		=> $this->_countStats (date ('Y-m-d', time ())),
			'yesterday'	=> $this->_countStats (date ('Y-m-d', time () - 86400)),
			'last_week'	=> $this->_countStats (array_map (function ($day) { return date ('Y-m-d', time () - ($day * 86400)); }, range (1, 7))),
		);
		return $stats;
	}


	private function _countStats ($days) {
		$query = "SELECT 
			SUM(`queries`) AS `queries`,
			SUM(`cached`) AS `cached`,
			SUM(`found`) AS `found`
				FROM `stats` WHERE `day`" .
			(is_array ($days) ?
			"IN ('" . implode ("','", $days) . "')"	
			:
			" = '$days'") ;
			$result = $this->db->query ($query);
			$data = $result->result_array ();
			if (empty ($data))
				return FALSE;
			return $data[0];
	}
}