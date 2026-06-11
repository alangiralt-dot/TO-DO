<?php

class TestController extends ApplicationController
{
	public function indexAction()
	{
		$this->view->tasks = [
			new Task('1', 'Design the app layout',       'Completed',   '08:00', '09:30', 'Alice Johnson'),
			new Task('2', 'Build the filter bar',         'Completed',   '09:45', '11:00', 'Alice Johnson'),
			new Task('3', 'Implement task table UI',      'In Progress', '11:15', '13:00', 'Bob Martinez'),
			new Task('4', 'Connect views to the layout',  'Pending',     '14:00', '15:00', 'Bob Martinez'),
			new Task('5', 'Write unit tests',             'Pending',     '15:30', '17:00', 'Carol Smith'),
		];
	}

	public function checkAction()
	{
		echo "hello from test::check";
	}
}
