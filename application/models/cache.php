<?php

class Cache extends CI_Model {

	public function getCache ($input) {
		if (	! (isset ($input['actor1']) OR is_numeric ($input['actor1'])) OR 
				! (isset ($input['actor2']) OR is_numeric ($input['actor2'])) OR
				( $input['actor1'] == $input['actor2']) 
			) {
//			throw new BadParamsException ("Cache->getCache BadParams " . var_dump ($input));
			return FALSE;
		}

		$cache = $this->_testByCache ($input['actor1'], $input['actor2']);

		if ($cache !== FALSE) {
			$this->db->set ('`used_cnt`', '`used_cnt` + 1', FALSE);
			$this->db->set ('`last_used`', 'NOW()', FALSE);
			$this->db->where ('id', $cache['id']);
			$cache_update = $this->db->update ('cache');

			return $this->_cacheToArray ($cache['data']);
		}
	}


	public function saveCache ($params) {
		if (	! (isset ($params['actor1']) OR is_numeric ($params['actor1'])) OR 
				! (isset ($params['actor2']) OR is_numeric ($params['actor2'])) OR
				! (isset ($params['data']) OR is_array ($params['data']) OR empty ($params['data'])) OR
				( $params['actor1'] == $params['actor2']) 
			) {
//			throw new BadParamsException ("Cache->saveCache Bad Params" . var_dump ($params));
			return FALSE;
		}
		$sql = array (
			'actor1'	=> $params['actor1']['id'],
			'actor2' 	=> $params['actor2']['id'],
			'data'		=> $this->_arrayToCache ($params['data']),
		);

		$result = $this->db->insert ('cache', $sql);

		if ( ! $result) {
//			throw new QueryException ("Cache->saveCache QueryException " . var_dump ($params));
			return FALSE;
		}
		return TRUE;
	}


	private function _testByCache ($actor1, $actor2) {
		$sql = "SELECT `id`, `data` FROM `cache`
									WHERE (
										(`actor1` = $actor1 AND `actor2` = $actor2) OR 
										(`actor2` = $actor1 AND `actor1` = $actor2)
									)";
		$query = $this->db->query ($sql);
		$result = $query->result_array ();
		if (empty ($result))
			return FALSE;
		return $result[0];
	}


	private function _cacheToArray ($cache) {
		return json_decode ($cache);
	}


	private function _arrayToCache ($data) {
		return json_encode ($data);
	}
}