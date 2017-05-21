<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 20/05/17
 * Time: 7:03 PM
 */

namespace Services;

use Models\RuleEngine;

class RuleEvaluatorService
{
    private $ruleEngine;
    private $dataQueue;
    private $observer = null;
    private $isProcessing = false;
    public function __construct(RuleEngine $ruleEngine, RuleEvaluatorServiceObserver $observer = null)
    {
        $this->ruleEngine = $ruleEngine;
        $this->dataQueue = [];
        $this->observer = $observer;
    }

    public function addDataToProcess($data){
        $this->dataQueue[] = $data;
        if (!$this->isProcessing) {
            $this->startProcessing();
        }
    }

    public function processData($data)
    {
        $data['parsed_value'] = $this->getTypeCastedData($data['value'], $data['value_type']);
        $response = $this->ruleEngine->evaluateData($data);
        if ($this->observer) {
            $this->observer->updateObserverResponse($data, $response['success'], $response['failed_rules']);
        }
        return $response['success'];
    }

    private function getTypeCastedData($value, $type)
    {
        return $type == 'Datetime' ? strtotime($value) : ( $type == 'String' ? '"'.$value.'"': $value);
    }

    private function startProcessing()
    {
        $this->isProcessing = true;
        while(count($this->dataQueue))
        {
            $this->processData($this->dataQueue[0]);
            array_shift($this->dataQueue);
        }
        $this->isProcessing = false;
    }
}