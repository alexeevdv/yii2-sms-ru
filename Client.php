<?php

namespace alexeevdv\sms-ru;

class Client extends \yii\base\Component {

    public $api_id;

    public function init() {

        parent::init();

        if (empty($this->api_id)){
            throw new \yii\base\InvalidConfigException("`api_id` param is required.");
        }
    }
}
