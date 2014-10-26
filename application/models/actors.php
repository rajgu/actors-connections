<?php

class Actors extends CI_Model {

	public function Actors () {
	}


	public function getByID ($input = FALSE) {
		if (! $this->_validateNumber ($input)) {
//			throw new BadParamsException ("Actors->getByID -> Niepoprawny parametr wejściowy " . var_dump ($input));
			return FALSE;
		}
		$where = $this->_buildSimpleWhere ('id', $input);
		$query = "SELECT * FROM `actors` $where";
		$result = $this->db->query ($query);
		$data = $result->result_array ();

		if (! $data OR empty ($data)) {
//			throw new NoDataException ("Actors->getByID -> Zapytanie: $query zwróciło:" . var_dump ($data));
			return FALSE;
		}

		return $data;
	}


	public function getByName ($input = FALSE) {
		if (! $this->_validateString ($input)) {
//			throw new BadParamsException ("Actors->getByName -> Niepoprawny parametr wejściowy " . var_dump ($input));
			return FALSE;
		}

		$length = strlen ($input);
		$where = $this->_buildFullTextWhere ('name', "\"$input\"");
		$query = "SELECT * FROM `actors` $where AND LENGTH(`name`) = $length LIMIT 0, 1";

		$result = $this->db->query ($query);
		$data = $result->result_array ();

		if (! $data OR empty ($data)) {
//			throw new NoDataException ("Actors->getByName -> Zapytanie: $query zwróciło:" . var_dump ($data));
			return FALSE;
		}

		return $data[0];
	}


	public function searchByName ($input = FALSE) {
		if (! $this->_validateString ($input)) {
//			throw new BadParamsException ("Actors->searchByName -> Niepoprawny parametr wejściowy " . var_dump ($input));
			return FALSE;
		}

		$where = $this->_buildFullTextWhere ('name', $input);
		$query = "SELECT * FROM `actors` $where LIMIT 0, 10";

		$result = $this->db->query ($query);
		$data = $result->result_array ();

		if (! $data OR empty ($data)) {
//			throw new NoDataException ("Actors->searchByName -> Zapytanie: $query zwróciło:" . var_dump ($data));
			return FALSE;
		}

		return $data;		
	}

	private function _buildSimpleWhere ($field, $data) {
		$query = "WHERE `$field` ";
		if (is_array ($data))
			$query .= 'IN (' . implode (',', array_map (array ($this ,"_quote"), $data)) . ') ';
		else
			$query .= ' = ' . $this->_quote ($data);
		return $query;		
	}


	private function _buildFullTextWhere ($field, $data, $use_wildcard = TRUE) {
		$query = "WHERE ";
		if (is_array ($data)) {
			$query .=	"MATCH ($field) AGAINST (" . 
						implode (" IN BOOLEAN MODE) AND MATCH ($field) AGAINST (",
						array_map ( function ($t) { return "'" . mysql_real_escape_string ($t) . "*'"; }, $data)) .
						' IN BOOLEAN MODE) ';
		} else {
			$query .= "MATCH ($field) AGAINST (" . $this->_quote ($data . ($use_wildcard ? '*' : '')) . " IN BOOLEAN MODE)";
		}
		return $query;
	}


	private function _validateString ($input) {
		if ( ! $input OR (
				is_array ($input) AND
				(empty ($input) OR ! $this->_checkArrayStringVars ($input))
			) OR (
				! is_array ($input) AND
				! is_string ($input)
			)
		)
			return FALSE;
		return TRUE;
	}


	private function _validateNumber ($input) {
		if ( ! $input OR (
					is_array ($input) AND 
					(empty ($input) OR ! $this->_checkArrayNumericVars ($input))
				) OR (
					! is_array ($input) AND
					(! is_numeric ($input) OR $input <= 0)
				)
			)
			return FALSE;
		return TRUE;		
	}


	protected function _quote ($string) {
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


	protected function _checkArrayStringVars ($input) {
		foreach ($input AS $var) {
			if (! is_string ($var)) {
				return FALSE;
			}
		}
		return TRUE;
	}
}