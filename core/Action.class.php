<?php

abstract class Action
{
	protected $auth = array();

	public function __construct()
	{}
	public function __destruct()
	{}

	abstract public function execute($context);

	public function forward()
	{}
};

?>
