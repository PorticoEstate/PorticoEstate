<?php
class BimObject {
	
	public function transformObjectToArray() {
		$reflection = new ReflectionObject($this);
		$publicMethods = $reflection->getMethods(ReflectionProperty::IS_PUBLIC);
		$result = array();
		foreach($publicMethods as $method) {
			/* @var $method ReflectionMethod */
			if(preg_match("/^get(.+)/", $method->getName(), $matches)) {
				$memberVarible = lcfirst($matches[1]);
				$value = $method->invoke($this);
				$result[$memberVarible] = $value;
			}
		}
		return $result;
	}
}