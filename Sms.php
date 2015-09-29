<?php

namespace alexeevdv\sms\ru;

class Sms extends \yii\base\Component {

    public $to;

    public $text;

    public function init() {

        parent::init();

        if (empty($this->to)) {
            throw new \yii\base\InvalidConfigException('`to` param is required');
        }

        if (empty($this->text)) {
            throw new \yii\base\InvalidConfigException('`text` param is required');
        }
    }
}
