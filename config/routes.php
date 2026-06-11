<?php 

/**
 * Used to define the routes in the system.
 * 
 * A route should be defined with a key matching the URL and an
 * controller#action-to-call method. E.g.:
 * 
 * '/' => 'index#index',
 * '/calendar' => 'calendar#index'
 */
$routes = array(
    '/test' => 'test#index',
    '/'                 => 'alan#open', // Open App
    '/alan/create' => 'alan#create', // Add Task
    '/tasks/add'   => 'alan#add' // Add Task
);
