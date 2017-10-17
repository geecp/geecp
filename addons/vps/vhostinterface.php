<?php
interface Vhost
{
	public function create($data=[]);

	public function suspend($data=[]);

	public function boot($data=[]);

	public function reboot($data=[]);

	public function shutdown($data=[]);

	public function addip($data=[]);

	//public function addip($data=[]);

}