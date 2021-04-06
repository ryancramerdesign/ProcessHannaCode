<?php namespace ProcessWire;

/**
 * Hanna Code
 * 
 * Copyright (C) 2021 by Ryan Cramer
 * Licensed under MPL 2.0
 * https://processwire.com
 * 
 * @property int $id
 * @property string $name
 * @property int $type
 * @property string $code
 * @property array $attrs
 * @property int $modified
 * @property int $accessed
 *
 */
class HannaCode extends WireData {

	/**
	 * Hanna code type: HTML
	 *
	 */
	const typeHTML = 0;

	/**
	 * Hanna code type: JS
	 *
	 */
	const typeJS = 1;

	/**
	 * Hanna code type: PHP
	 *
	 */
	const typePHP = 2;

	/**
	 * Flag that indicates Hanna code should not consume surrounding <p> tags
	 *
	 */
	const typeNotConsuming = 4;

	/**
	 * Types to type names
	 *
	 * @var array
	 *
	 */
	protected $typeNames = array(
		self::typePHP => 'PHP',
		self::typeJS => 'JS',
		self::typeHTML => 'HTML',
	);

	/**
	 * Default values for blank HannaCode
	 *
	 * @var array
	 *
	 */
	protected $defaults = array(
		'id' => 0,
		'name' => '',
		'type' => 0,
		'code' => '',
		'modified' => 0,
		'accessed' => 0,
		'attrs' => array(),
	);

	protected $attrs = '';

	/**
	 * Construct
	 *
	 */
	public function __construct() {
		$this->setArray($this->defaults);
		parent::__construct();
	}

	/**
	 * Set property
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return self|WireData
	 *
	 */
	public function set($key, $value) {
		if($key === 'attrs') {
			$this->attrs($value);
			return $this;
		}
		if(isset($this->defaults[$key]) && is_int($this->defaults[$key])) {
			$value = (int) $value;
		}
		return parent::set($key, $value);
	}

	/**
	 * Get or set attrs
	 *
	 * @param array|string|null $value
	 * @return array|mixed|null|string
	 *
	 */
	public function attrs($value = null) {
		if($value === null) {
			return parent::get('attrs');
		}
		if(is_string($value)) {
			$attrs = array();
			foreach(explode("\n", $value) as $line) {
				if(strpos($line, '=')) {
					list($name, $value) = explode('=', $line, 2);
					$name = trim($name);
					$value = trim($value, '"\'' );
				} else {
					$name = trim($line);
					$value = '';
				}
				if(!empty($name)) $attrs[$name] = $value;
			}
			$value = $attrs;
		}
		if(is_array($value)) {
			parent::set('attrs', $value);
			return $value;
		}
		return array();
	}

	/**
	 * Set type by type[name] constant (int) or name (string)
	 *
	 * This retains an existing typeNotConsuming value
	 *
	 * @param int|string $set
	 * @throws WireException
	 *
	 */
	public function setType($set) {
		if(!is_int($set)) $set = $this->nameToType($set);
		if(!is_int($set)) throw new WireException("Invalid setType() argument");
		$type = parent::get('type');
		$nc = $type & self::typeNotConsuming;
		if($nc) $type -= self::typeNotConsuming;
		if($type != $set) $type = $set;
		if($nc) $type = $type | self::typeNotConsuming;
		parent::set('type', $type);
	}

	/**
	 * Does this Hanna code have given type?
	 *
	 * @param string|int $type
	 * @return int
	 *
	 */
	public function hasType($type) {
		if(is_string($type)) $type = $this->nameToType($type);
		$val = parent::get('type');
		return ($val & $type);
	}

	/**
	 * Get current typeName or name of given type integer
	 *
	 * @param null|int $type
	 * @return string
	 *
	 */
	public function typeName($type = null) {
		$typeName = '';
		if($type === null) $type = parent::get('type');
		foreach($this->typeNames as $val => $name) {
			if($type & $val) $typeName = $name;
			if($typeName) break;
		}
		return $typeName;
	}

	/**
	 * Code type value only (no consuming flag)
	 * 
	 * @return int
	 * 
	 */
	public function codeType() {
		$type = (int) parent::get('type');
		if($type & self::typeNotConsuming) $type -= self::typeNotConsuming; 
		return $type;
	}

	/**
	 * @param string $name
	 * @return false|int
	 *
	 */
	public function nameToType($name) {
		if(ctype_digit("$name")) return (int) $name;
		return array_search(strtoupper($name), $this->typeNames);
	}

	/**
	 * @return bool
	 * 
	 */
	public function isPHP() {
		$type = (int) parent::get('type');
		return (bool) ($type & self::typePHP);
	}

	/**
	 * @return bool
	 * 
	 */
	public function isJS() {
		$type = (int) parent::get('type');
		return (bool) ($type & self::typeJS);
	}

	/**
	 * @return bool
	 * 
	 */
	public function isHTML() {
		return (!$this->isPHP() && !$this->isJS());
	}

	/**
	 * Get or set consuming state
	 *
	 * @param null|bool $set
	 * @return bool
	 *
	 */
	public function isConsuming($set = null) {
		if($set !== null) $set = !$set;
		return !$this->isNotConsuming($set);
	}

	/**
	 * Get or set NOT consuming state
	 *
	 * @param null|bool $set
	 * @return bool
	 *
	 */
	public function isNotConsuming($set = null) {
		$type = (int) parent::get('type');
		$isNotConsuming = ($type & self::typeNotConsuming);
		if($set === true) {
			if(!$isNotConsuming) parent::set('type', $type | self::typeNotConsuming);
		} else if($set === false) {
			if($isNotConsuming) parent::set('type', $type - self::typeNotConsuming);
		}
		return $isNotConsuming;
	}
	
}
