<?php

class a2m extends CI_Model {

	public function a2m () {
	}


	public function getMoviesByActorID ($input, $limit = FALSE) {

		$where = $this->_buildSimpleWhere ('actor_id', $input);
		$limit = $this->_buildSimpleLimit ($limit);
		$query = "SELECT `movie_id` FROM `a2m` $where $limit";
		$result = $this->db->query ($query);
		$data = $result->result_array ();

		if (! $data OR empty ($data)) {
			throw new NoDataException ("A2M->getMoviesByActorID -> Zapytanie: $query zwróciło:" . var_dump ($data));
			return FALSE;
		}

		return array_map (function ($tmp) {return $tmp['movie_id'];}, $data);
	}


	public function getActorsByMovieID ($input, $limit = FALSE) {

		$where = $this->_buildSimpleWhere ('movie_id', $input);
		$limit = $this->_buildSimpleLimit ($limit);
		$query = "SELECT `actor_id` FROM `a2m` $where $limit";
		$result = $this->db->query ($query);
		$data = $result->result_array ();

		if (! $data OR empty ($data)) {
			throw new NoDataException ("A2M->getActorsByMovieID -> Zapytanie: $query zwróciło:" . var_dump ($data));
			return FALSE;
		}

		return array_map (function ($tmp) {return $tmp['actor_id'];}, $data);
	}


	public function getActorsConnection ($actor1, $actor2) {
		for ($loop=1; $loop <= 4; $loop++) {
			$include_count = 1;
			// Tworzymy zwracane wartości:
			$sql  = "SELECT\n";
			$sql .= "\t`a1`.`movie_id` AS `movie_1`,\n";
			for ($i = 2; $i <= $loop; $i++) {
				$sql .= "\t`a" . ($i * 2 - 2) . "`.`actor_id` AS `actor_" . ($i - 1) . "`,\n";
				$sql .= "\t`a" . ($i * 2 - 1) . "`.`movie_id` AS `movie_{$i}`,\n";
			}
			$sql .= "\t1\n";
			$sql .= "FROM `a2m` AS `a1`\n";
			$sql .= "INNER JOIN `a2m` AS `a2`\n";
			$sql .= "\tON `a1`.`movie_id` = `a2`.`movie_id`\n";

			for ($i = 2; $i <= $loop; $i++) {
				$include_count += 2;
				$sql .= "INNER JOIN `a2m` AS `a" . ($i * 2 - 1) . "`\n";
				$sql .= "\tON `a" . ($i * 2 - 2) . "`.`actor_id` = `a" . ($i * 2 - 1) . "`.`actor_id`\n";
				$sql .= "INNER JOIN `a2m` AS `a" . ($i * 2 ) . "`\n";
				$sql .= "\tON `a" . ($i * 2 - 1) . "`.`movie_id` = `a" . ($i * 2) . "`.`movie_id`\n";
			}

			$sql .= "\tAND `a" . ($include_count + 1). "`.`actor_id` = $actor2\n";
			$sql .= "WHERE\n\t`a1`.`actor_id` = $actor1\n";
			$sql .= "LIMIT 1";

			$query = $this->db->query ($sql);
			$result = $query->result_array ();

			if ( ! empty ($result))
				return $result[0];
		}
		return FALSE;
	}


	public function transformResults ($connection, $actor1, $actor2) {
		$return = array ();
		$return[] = array (
			'type' => 'actor',
			'data' => $actor1['name']
		);

		$transformed = array ();

		foreach ($connection AS $type => $id) {
			if (strpos($type, 'movie') !== FALSE) {
				$movie = $this->movies->getByID ($id);
				if (! $movie)
					return FALSE;
				$return[] = array (
					'type'	=> 'movie',
					'data'	=> $movie[0]['title'],
				);
			}
			if (strpos($type, 'actor') !== FALSE) {
				$actor = $this->actors->getByID ($id);
				if (! $actor)
					return FALSE;
				$return[] = array (
					'type'	=> 'actor',
					'data'	=> $actor[0]['name'],
				);
			}
		}
		$return[] = array (
			'type' => 'actor',
			'data' => $actor2['name']
		);
		if (empty ($return))
			return FALSE;
		return $return;
	}


	private function _buildSimpleLimit ($limit = FALSE) {
		if ( ! $limit)
			return '';
		if (isset ($limit['offset']) AND $limit['limit'])
			return "LIMIT {$limit['offset']}, {$limit['limit']}";
		if (isset ($limit['offset']) AND ! isset($limit['limit']))
			return "LIMIT {$limit['offset']}, 999999";
		if ( ! isset ($limit['offset']) AND isset ($limit['limit']))
			return "LIMIT {$limit['limit']}";
		return '';
	}


	private function _buildSimpleWhere ($field, $data) {
		$query = "WHERE `$field` ";
		if (is_array ($data))
			$query .= 'IN (' . implode (',', $data) . ') ';
		else
			$query .= ' = ' . $data;
		return $query;
	}

}
