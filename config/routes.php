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
    '/alan/add'   => 'alan#add', // Add Task
    '/alan/edit/:id' => 'alan#edit', // Update Task
    '/alan/update' => 'alan#update' // Update Task
);
