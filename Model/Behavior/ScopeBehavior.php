<?php
App::uses('Model', 'Model');
App::uses('ModelBehavior', 'Model');

class ScopeBehavior extends ModelBehavior {

	protected $_virtualFields = array();

/**
 * Initiate behavior for the model using specified settings.
 *
 * Available settings:
 *
 * @param Model $Model Model using the behavior
 * @param array $settings Settings to override for model.
 * @return void
 */
	public function setup(Model $Model, $settings = array()) {
		foreach ($Model->scopes as $type => $options) {
			$this->mapMethods = array('/^_find' . ucfirst($type) . '$/' => '_find' . ucfirst($type));
			$Model->findMethods[$type] = true;
		}
	}

/**
 * Magic __call function to re-wrote a scoped find to the behavior
 *
 * @param string $method Model using the behavior
 * @param mixed $params Array of arguments for the called method
 * @return void
 */
	public function __call($method, $params) {
		$type = lcfirst(preg_replace('/^_find/', '', $method));
		if ($type === $method || empty($params) || !($params[0] instanceof Model)) {
			throw new CakeException(__d('cake_dev', 'Method %s does not exist', $method));
		}

		$results = isset($params[4]) ? $params[4] : array();
		return $this->_findScoped($params[0], $type, $params[2], $params[3], $results);
	}

/**
 * Handles the before/after filter logic for find('scoped') operations. Only called by Model::find().
 *
 * @param Model $Model Model using the behavior
 * @param array $type type of query
 * @param string $state Either "before" or "after"
 * @param array $query Query.
 * @param array $results Results.
 * @return array
 * @see Model::find()
 */
	protected function _findScoped(Model $Model, $type, $state, $query, $results = array()) {
		if (empty($Model->scopes)) {
			throw new CakeException(__d('cake_dev', 'Method %s does not exist', $method));
		}

		if (!isset($Model->scopes[$type])) {
			throw new CakeException(__d('cake_dev', 'Method %s does not exist', $method));
		}

		$config = $Model->scopes[$type];

		if ($state === 'before') {
			if (!empty($config['find']['virtualFields'])) {
				$this->_virtualFields[$Model->alias] = $Model->virtualFields;
				$Model->virtualFields = $config['find']['virtualFields'];
			}

			if (!empty($config['find']['options']['contain'])) {
				$Model->Behaviors->attach('Containable');
			}

			foreach ($query as $key => $value) {
				if ($value === null && isset($config['find']['options'][$key])) {
					$query[$key] = $config['find']['options'][$key];
				}
			}

			$query = Set::merge($config['find']['options'], $query);
			$method = '_find' . ucfirst($config['find']['type']);
			if ($method == '_findAll') {
				return $query;
			}

			return $Model->$method($state, $query, $results);
		}

		if (!empty($config['find']['virtualFields'])) {
			$Model->virtualFields = $this->_virtualFields[$Model->alias];
			$this->_virtualFields[$Model->alias] = null;
		}

		$method = '_find' . ucfirst($config['find']['type']);
		if ($method == '_findAll') {
			return $results;
		}

		return $Model->$method($state, $query, $results);
	}

/**
 * Alias for scoped find
 *
 * @param Model $Model Model using the behavior
 * @param array $type type of query
 * @param string $options Array of options for the find
 * @return array
 * @see Model::find()
 */
	public function scopedFind(Model $Model, $type, $options = array()) {
		return $Model->find($type, $options);
	}

/**
 * Returns a list of scoped finds available
 *
 * @param Model $Model Model using the behavior
 * @return array
 */
	public function scopes(Model $Model) {
		if (empty($Model->scopes)) {
			return array();
		}

		$data = array();
		foreach ($Model->scopes as $group => $config) {
			$data[$group] = $config['name'];
		}

		return $data;
	}
}
