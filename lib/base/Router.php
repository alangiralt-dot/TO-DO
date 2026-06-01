<?php

/**
 * Used for setting up the routing in the system
 * EDB 06-1-26: Defines the specific controller to be instantiated based on the URI passed on the request:
 *  if matches a specified route in the routliste instantiates a controller based on the parameters of the routes list, a trigers an action specifict to the controllers class.
 */
class Router
{
	/**
	 * Executes the system routing
	 * @throws Exception
	 */
	public function execute($routes)
	{
		// tries to find the route and run the given action on the controller
		try {
			// the controller and action to execute
			$controller = null;
			$action = null;

			// tries to find a simple route
			$routeFound = $this->_getSimpleRoute($routes, $controller, $action);
			//EDB 06-1-26: _getSimpleRoute is a generic method to retrieve the a controller and an action in the routes list. It probably fails for routes with parameters
			if (!$routeFound) {
				// tries to find the a matching "parameter route"
				//EDB 06-1-26: this method is more sophisticated compared to _getSimpleRoute as it handles the parameters that are part of the route.
				$routeFound = $this->_getParameterRoute($routes, $controller, $action);
			}

			// no route found, throw an exception to run the error controller
			if (!$routeFound || $controller == null || $action == null) {
				throw new Exception('no route added for ' . $_SERVER['REQUEST_URI']);
			} else {
				// executes the action on the controller
				$controller->execute($action);
			}
		} catch (Exception $exception) {
			// runs the error controller
			$controller = new ErrorController();
			$controller->setException($exception);
			$controller->execute('error');
		}
	}

	/**
	 * Tests if a route has parameters
	 * @param string $route the route (uri) to test
	 * @return boolean
	 */
	public function hasParameters($route) //EDB 06-1-26: this fucntion is used to evaluate if a uri has parameters in _getParameterRoute()
	{
		return preg_match('/(\/:[a-z]+)/', $route);
	}

	/**
	 * Fetches the current URI called
	 * @return string the URI called
	 */
	protected function _getUri()
	{
		$uri = explode('?', $_SERVER['REQUEST_URI']);
		$uri = $uri[0];
		$uri = substr($uri, strlen(WEB_ROOT));

		return $uri;
	}

	/**
	 * Tries to find a matching simple route
	 * @param array $routes the list of routes in the system
	 * @param Controller $controller the controller to use (sent as reference)
	 * @param string $action the action to execute (sent as reference)
	 * @return boolean
	 */
	protected function _getSimpleRoute($routes, &$controller, &$action) //EDB 06-1-26: it points to the global reference: action and controler in execute()
	{
		// fetches the URI
		$uri = $this->_getUri();

		// if the route isn't defined, try to add a trailing slash
		if (isset($routes[$uri])) { //EDB 06-1-26: the request URI exists in the routes matrix, it is retreive from the matrix.
			$routeFound = $routes[$uri];
		} else if (isset($routes[$uri . '/'])) {
			$routeFound = $routes[$uri . '/']; //EDB 06-1-26: the request URI does not exist is incorrect, new attempt with separator.
		} else {
			$uri = substr($uri, 0, -1);
			// fetches the current route
			$routeFound = isset($routes[$uri]) ? $routes[$uri] : false; //EDB 06-1-26: tries retreiving the uri from the array without '/'
		}

		// if a matching route was found
		if ($routeFound) {
			list($name, $action) = explode('#', $routeFound); //EDB 06-1-26: Decomposes the route format Controller#Action, action = method

			// initializes the controller
			$controller = $this->_initializeController($name);

			return true;
		}

		return false;
	}

	/**
	 * Tries to find a matching parameter route
	 * @param array $routes the list of routes in the system
	 * @param Controller $controller the controller to use (sent as reference)
	 * @param string $action the action to execute (sent as reference)
	 * @return boolean
	 */
	protected function _getParameterRoute($routes, &$controller, &$action)
	{
		// fetches the URI
		$uri = $this->_getUri();

		// testing routes with parameters
		foreach ($routes as $route => $path) {
			if ($this->hasParameters($route)) {
				$uriParts = explode('/:', $route); //EDB 06-1-26: se separan los parametros de la ruta e.g. /Usuarios#TaskList/:id=akdabime42s5 -> uriParts = ['/Usuarios#TaskList','id=akdabime42s5']

				$pattern = '/^';
				//$pattern .= '\\'.($uriParts[0] == '' ? '/' : $uriParts[0]); -- EDB 06-1-26: to be ignored!!
				/*EDB 06-1-26: a regex pattern is going to be built here:
				*	1. If first part of uri is not defined $uriParts[0] == '' add a separator (escaped bar) '/^\/
				*	2. If not empty add repalce the separators with escaped bars e.g '/Usuarios#TaskList' -> '/^\/Usuarios#TaskList\/'
				*	3. For each parameter add a regex pattern except the first part, e.g. '/^\/Usuarios#TaskList\/([a-zA-Z0-9]+)'
				*	4. add end slashes pattern e.g. '/^\/Usuarios\/([a-zA-Z0-9]+)[\/]{0,1}$/'
				*/
				if ($uriParts[0] == '') {
					$pattern .= '\\/';
				} else {
					$pattern .= str_replace('/', '\\/', $uriParts[0]);
				}

				foreach (range(1, count($uriParts) - 1) as $index) {
					$pattern .= '\/([a-zA-Z0-9]+)';
				}

				// now also handles ending slashes!
				$pattern .= '[\/]{0,1}$/';

				$namedParameters = array();
				//EDB 06-1-26: Verify that the parameters are correctly formated in regard of the regex expression: ([a-zA-Z0-9]+) and return matches in $namedParameters
				$match = preg_match($pattern, $uri, $namedParameters);
				// if the route matches
				if ($match) {
					list($name, $action) = explode('#', $path); //EDB 06-1-26: decomposed controller and action

					// initializes the controller
					$controller = $this->_initializeController($name); //EDB 06-1-26: method to start controller

					// adds the named parameters to the controller, EDB 06-1-26: with respective value!
					foreach (range(1, count($namedParameters) - 1) as $index) {
						$controller->addNamedParameter(
							$uriParts[$index],
							$namedParameters[$index]
						);
					}

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Initializes the given controller
	 * @param string $name the name of the controller
	 * @return mixed null if error, else a controller
	 */
	protected function _initializeController($name)
	{
		// initializes the controller
		$controller = ucfirst($name) . 'Controller';
		// constructs the controller
		return new $controller();
	}
}
