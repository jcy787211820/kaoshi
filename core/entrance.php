<?php
namespace core;
require_once 'function.php';
/**
 * entrance file
 */
Class Entrance
{
	/**
	 * run
	 */
	public static function run()
	{
		/*
		 * Set this project base const.
		 */
		Config::base();

		/*
		 * Excuate controller.
		 */
		Router::action();
	}
}