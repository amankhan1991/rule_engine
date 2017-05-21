<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 20/05/17
 * Time: 8:08 PM
 */


function __autoload($class)
{
    $parts = explode('\\', $class);
    $className = join('/', $parts);
    require $className . '.php';
}


use Models\Rule;
use Models\RuleEngine;
use Services\RuleEvaluatorServiceObserver;
use Services\RuleEvaluatorService;

class testRuleService implements RuleEvaluatorServiceObserver
{
    public function updateObserverResponse($data, $success, $failedRules)
    {
        if (!$success) {
            echo $data['signal']." ".$data['value'].'------';
            foreach ($failedRules as $rule) {
                echo (string)$rule." ";
            }
            echo PHP_EOL;
        }
    }

    public function startTest()
    {
        $ruleEngine = new RuleEngine();
//        $this->addRules($ruleEngine);
        $service = new RuleEvaluatorService($ruleEngine, $this);
        $data = json_decode(file_get_contents('test_data.json'), true);
        foreach ($data as $datum) {
            $service->addDataToProcess($datum, true);
        }
    }

    public function addRules($ruleEngine)
    {
        $rule = new Rule('ATL1', Rule::SHOULD_NOT_RISE_ABOVE, 240.00);
        $ruleEngine->addRule($rule);
        $rule = new Rule('ATL2', Rule::SHOULD_NEVER_BE, Rule::LOW);
        $ruleEngine->addRule($rule);
        $rule = new Rule('ATL3', Rule::SHOULD_NOT_BE_IN, Rule::FUTURE);
        $ruleEngine->addRule($rule);
    }
}

$x = new testRuleService();
$x->startTest();