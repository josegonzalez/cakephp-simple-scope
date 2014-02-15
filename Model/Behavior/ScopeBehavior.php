<?php
App::uses('Model', 'Model');
App::uses('ModelBehavior', 'Model');

class ScopeBehavior extends ModelBehavior {

    protected $contains = array();

    protected $virtualFields = array();

    public function setup(Model $model, $config = array()) {
        foreach ($model->scopes as $type => $options) {
            $this->mapMethods = array('/^_find' . ucfirst($type) .'$/' => '_find' . ucfirst($type));
            $model->findMethods[$type] = true;
        }
    }

    public function __call($method, $params) {
        $type = lcfirst(preg_replace('/^_find/', '', $method));
        if ($type == $method || empty($params) || !($params[0] instanceOf Model)) {
            throw new CakeException(__d('cake_dev', 'Method %s does not exist', $method));
        }

        $results = isset($params[4]) ? $params[4] : array();
        return $this->_findScoped($params[0], $type, $params[2], $params[3], $results);
    }

    protected function _findScoped(Model $model, $type, $state, $query, $results = array()) {
        if (empty($model->scopes)) {
            throw new CakeException(__d('cake_dev', 'Method %s does not exist', $method));
        }

        if (!isset($model->scopes[$type])) {
            throw new CakeException(__d('cake_dev', 'Method %s does not exist', $method));
        }

        $config = $model->scopes[$type];

        if ($state === 'before') {
            if (!empty($config['find']['virtualFields'])) {
                $this->virtualFields[$model->alias] = $model->virtualFields;
                $model->virtualFields = $config['find']['virtualFields'];
            }

            if (!empty($config['find']['options']['contain'])) {
                $model->Behaviors->attach('Containable');
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

            return $model->$method($state, $query, $results);
        }

        if (!empty($config['find']['virtualFields'])) {
            $model->virtualFields = $this->virtualFields[$model->alias];
            $this->virtualFields[$model->alias] = null;
        }

        $method = '_find' . ucfirst($config['find']['type']);
        if ($method == '_findAll') {
            return $results;
        }

        return $model->$method($state, $query, $results);
    }

    public function scopedFind(Model $model, $type, $options = array()) {
        return $model->find($type, $options);
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
