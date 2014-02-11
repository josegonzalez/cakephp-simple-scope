<?php
App::uses('ModelBehavior', 'Model');

class ScopeBehavior extends ModelBehavior {

	public function scopedFind(Model $model, $type) {
		if (empty($model->scopes)) {
			return array();
		}

		if (!isset($model->scopes[$type])) {
			return array();
		}

		$config = $model->scopes[$type];
		if ($config['find'] === null) {
			return array();
		}

		if (!empty($config['find']['virtualFields'])) {
			$model->virtualFields = $config['find']['virtualFields'];
		}

		if (!empty($config['find']['options']['contain'])) {
			$model->Behaviors->attach('Containable');
		}

		return $model->find($config['find']['type'], $config['find']['options']);
	}

	public function scopes(Model $model) {
		if (empty($model->scopes)) {
			return array();
		}

		$data = array();
		foreach ($model->scopes as $group => $config) {
			$data[$group] = $config['name'];
		}

		return $data;
	}
}
