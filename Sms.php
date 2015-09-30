<?php

namespace alexeevdv\sms\ru;

class Sms extends \yii\base\Model {

    /**
     * Sms identificator
     * @var string
     */
    public $id;

    /**
     * Sms sending result
     * @var integer
     */
    public $code = -1;

    public $to;

    public $text;

    public $test;

    public $translit = false;

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
