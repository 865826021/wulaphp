<?php
namespace wulaphp\conf;

/**
 * 系统配置类.
 *
 * @author leo
 *
 */
class Configuration implements \ArrayAccess, \IteratorAggregate {

	protected $settings = array();

	protected $name;

	public function __construct($name = 'default') {
		$this->name = $name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function name() {
		return $this->name;
	}

	public function setConfigs($settings = []) {
		$this->settings = $settings;
	}

	public function offsetExists($offset) {
		return isset ($this->settings [ $offset ]);
	}

	public function offsetGet($offset) {
		return $this->get($offset, null);
	}

	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	public function offsetUnset($offset) {
		unset ($this->settings [ $offset ]);
	}

	/**
	 * 获取设置.
	 *
	 * @param string $name
	 * @param string $default
	 *
	 * @return string
	 */
	public function get($name, $default = '') {
		return isset ($this->settings [ $name ]) ? $this->settings [ $name ] : $default;
	}

	public function getb($name, $default = false) {
		$v = $this->get($name, $default);

		return (bool)$v;
	}

	public function geti($name, $default = 0) {
		$v = $this->get($name, $default);

		return (int)$v;
	}

	/**
	 * 设置.
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public function set($name, $value) {
		$this->settings [ $name ] = $value;
	}

	/*
	 * (non-PHPdoc) @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {
		return new \ArrayIterator ($this->settings);
	}

	public function toArray() {
		return $this->settings;
	}
}