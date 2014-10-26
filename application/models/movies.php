<?php

class Movies extends CI_Model {

	public function Movies () {
	}


	public function getByID ($input = FALSE) {

		if (! $this->_validateNumber ($input)) {
			throw new BadParamsException ("Actors->getByID -> Niepoprawny parametr wejściowy " . var_dump ($input));
			return FALSE;
		}
		$where = $this->_buildSimpleWhere ('id', $input);
		$query = "SELECT * FROM `movies` $where";
		$result = $this->db->query ($query);
		$data = $result->result_array ();

		if (! $data OR empty ($data)) {
			throw new NoDataException ("Actors->getByID -> Zapytanie: $query zwróciło:" . var_dump ($data));
			return FALSE;
		}

		return $data;
	}


	private function _buildSimpleWhere ($field, $data) {
		$query = "WHERE `$field` ";
		if (is_array ($data))
			$query .= 'IN (' . implode (',', array_map (array ($this ,"_quoteString"), $data)) . ') ';
		else
			$query .= ' = ' . $this->_quoteString ($data);
		return $query;
	}


	private function _validateString ($input) {
		if (is_string ($input))
			return TRUE;
		return FALSE;
	}


	private function _validateNumber ($input) {
		if ( ! $input OR (
					is_array ($input) AND 
					(empty ($input) OR ! $this->_checkArrayNumericVars ($input))
				) OR (
					is_int ($input) AND
					$input > 0
				)
			)
			return FALSE;
		return TRUE;		
	}


	protected function _quoteString ($string) {
		if (! is_numeric ($string))
			return '\'' . mysql_real_escape_string ($string) . '\'';
		return $string;
	}


	protected function _checkArrayNumericVars ($input) {
		foreach ($input AS $var) {
			if (! is_numeric ($var)) {
				return FALSE;
			}
		}
		return TRUE;
	}
}