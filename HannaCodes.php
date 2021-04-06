<?php namespace ProcessWire;

/**
 * Hanna Code
 *
 * Copyright (C) 2021 by Ryan Cramer
 * Licensed under MPL 2.0
 * https://processwire.com
 *
 */

require_once(__DIR__ . '/HannaCode.php');

/**
 * Hanna Codes
 * 
 */
class HannaCodes extends Wire {
	
	const table = 'hanna_code';

	/**
	 * Get Hanna code by name or id
	 * 
	 * @param string|int $key
	 * @return HannaCode
	 * 
	 */
	public function get($key) {
		
		$h = $this->getNew();
		$sql = 'SELECT * FROM ' . self::table . ' ';
		
		if(ctype_digit("$key")) {
			$sql .= 'WHERE `id`=:key';
		} else {
			$sql .= 'WHERE `name`=:key';
		}
		
		$query = $this->wire()->database->prepare($sql);
		$query->bindValue(':key', $key);
		$query->execute();
		
		if(!$query->rowCount()) return $h;
		
		$row = $query->fetch(\PDO::FETCH_ASSOC);
		$query->closeCursor();
		list($row['code'], $row['attrs']) = $this->unpackCode($row['code']);
		$h->setArray($row); 
		
		return $h;
	}

	/**
	 * Get all Hanna Codes
	 * 
	 * @param string $sort
	 * @return array|HannaCode[]
	 * @throws WireException
	 * 
	 */
	public function getAll($sort = 'name') {
		
		$sorts = array(
			'name' => 'name', 
			'-name' => 'name DESC',
			'modified' => 'modified', 
			'-modified' => 'modified DESC',
			'accessed' => 'accessed',
			'-accessed' => 'accessed ASC',
		);
		
		if(!isset($sorts[$sort])) $sort = 'name';
		
		$sql = 'SELECT * FROM ' . self::table . ' ORDER BY :sort';
		
		$query = $this->wire()->database->prepare($sql);
		$query->bindValue(':sort', $sorts[$sort]);
		$query->execute();
		
		$a = array();
		while($row = $query->fetch(\PDO::FETCH_ASSOC)) {
			list($row['code'], $row['attrs']) = $this->unpackCode($row['code']);
			$h = $this->getNew($row);
			$a[] = $h;
		}
		
		$query->closeCursor();
	
		return $a;
	}

	/**
	 * Get new HannaCode
	 * 
	 * @param array $data
	 * @return HannaCode
	 * 
	 */
	public function getNew(array $data = array()) {
		$h = new HannaCode();
		$this->wire($h);
		if(count($data)) $h->setArray($data);
		return $h;
	}

	/**
	 * Update accessed time of Hanna Code
	 * 
	 * @param HannaCode $h
	 * @return bool
	 * 
	 */
	public function touch(HannaCode $h) {
		$sql = 'UPDATE ' . self::table . ' SET accessed=:time WHERE id=:id';
		$query = $this->wire()->database->prepare($sql);
		$query->bindValue(':time', time()); 
		$query->bindValue(':id', $h->id); 
		return $query->execute();
	}

	/**
	 * Save Hanna code
	 * 
	 * @param HannaCode $h
	 * @return bool
	 * @throws WireException
	 * 
	 */
	public function save(HannaCode $h) {
		$database = $this->wire()->database;
		
		$sql =
			($h->id ? "UPDATE " : "INSERT INTO ") . self::table . ' ' .
			"SET `name`=:name, `type`=:type, `code`=:code, `modified`=:modified " .
			($h->id ? "WHERE id=:id" : "");
		
		$code = $this->packCode($h->code, $h->attrs); 
		$name = $h->name;
		$result = false;
		$n = 0;

		do {
			$retry = false;
			$query = $database->prepare($sql);
			$query->bindValue(':name', $h->name);
			$query->bindValue(':type', $h->type, \PDO::PARAM_INT);
			$query->bindValue(':code', $code);
			$query->bindValue(':modified', time());
			if($h->id) $query->bindValue(':id', $h->id, \PDO::PARAM_INT);
			try {
				$result = $query->execute();
			} catch(\Exception $e) {
				if($e->getCode() == 23000 && !$h->id) { // resolve duplicate name
					$retry = true;
					$h->name = $name . '-' . (++$n);
				}
			}
		} while($retry);

		if($result && !$h->id) {
			$h->id = $database->lastInsertId();
		}
	
		return $result;
	}

	/**
	 * Delete 
	 * 
	 * @param HannaCode $h
	 * @return bool
	 * @throws WireException
	 * 
	 */
	public function delete(HannaCode $h) {
		$query = $this->wire()->database->prepare("DELETE FROM " . self::table . " WHERE id=:id LIMIT 1");
		$query->bindValue(":id", $h->id);
		return $query->execute();
	}
	
	/**
	 * Extract default attributes stored in the code block
	 *
	 * Extracts hc_attr\nkey=value\nkey2=value2\nhc_attr to [ 'key' => $value, 'key2' => 'value2' ];
	 * and removes the section from $code
	 *
	 * @param string $code
	 * @return array
	 *
	 */
	public function unpackCode($code) {

		$pos = strpos($code, 'hc_attr*/');
		if($pos === false) return array($code, '');

		$attrStr = trim(substr($code, 9, $pos-9));
		$code = trim(substr($code, $pos+10));
		$lines = explode("\n", $attrStr);
		$attrs = array();

		foreach($lines as $line) {
			$pos = strpos($line, '=');
			$name = substr($line, 0, $pos);
			$value = substr($line, $pos);
			$name = trim($name, "\r\n=");
			if(empty($name)) continue;
			// filter out API variable names and reserved words
			if($this->wire($name) !== null || in_array($name, array('name', 'hanna', 'attr'))) $name = "_$name";
			$value = trim($value, "\r\n=\"");
			$attrs[$name] = $value;
		}

		return array($code, $attrs);
	}

	/**
	 * Pack attrs into $code
	 * 
	 * @param string $code
	 * @param string|array $attr
	 * @return string
	 * 
	 */
	public function packCode($code, $attr) {
		$sanitizer = $this->wire()->sanitizer;
		$out = '';
		if(is_array($attr)) {
			$attrs = array();
			foreach($attr as $name => $value) {
				$attrs[] = strlen($value) ? "$name=$value" : "$name";
			}
		} else {
			$attrs = explode("\n", $attr); 
		}
		foreach($attrs as $line) {
			$line = trim($line);
			if(strpos($line, '=')) {
				list($attrName, $attrValue) = explode('=', $line);
				$attrName = $sanitizer->fieldName(trim($attrName));
				$attrValue = '"' . str_replace('"', '\"', $attrValue) . '"';
				$attrValue = str_replace(array('/*', '*/', 'hc_attr'), '', $attrValue);
			} else {
				$attrName = $line;
				$attrValue = '""';
			}
			if(empty($attrName)) continue;
			if($this->wire($attrName) || in_array($attrName, array('name', 'hanna', 'attr'))) {
				$this->error($this->_('Disallowed attribute name:') . " $attrName");
				$attrName = '_' . $attrName;
			}
			$out .= "$attrName=$attrValue\n";
		}
		if($out) $code = "/*hc_attr\n{$out}hc_attr*/\n" . $code;
		return $code;
	}
	
	public function install() {
		$sql =
			"CREATE TABLE " . self::table . " (" .
			"`id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY, " .
			"`name` varchar(128) NOT NULL, " .
			"`type` tinyint NOT NULL DEFAULT 0, " .
			"`code` text, " .
			"`modified` int unsigned NOT NULL default 0, " .
			"`accessed` int unsigned NOT NULL default 0, " .
			"UNIQUE `name`(`name`)"  .
			")";
		$this->wire()->database->exec($sql);
	}
	
	public function uninstall() {
		try {
			$this->wire()->database->exec("DROP TABLE " . self::table);
		} catch(\Exception $e) {
			$this->error($e->getMessage());
		}
	}
	
}

