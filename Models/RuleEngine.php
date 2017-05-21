<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 20/05/17
 * Time: 5:26 PM
 */

namespace Models;

class RuleEngine
{
    private $id;
    private $rules = [];
    private $latestVersion = null;
    private $ruleEngineName;

    const FILE_NAME = "rule_json_data/rule_engine.json";
    /**
     * RuleEngine constructor.
     * @param $ruleEngineName
     */
    public function __construct($ruleEngineName = null, $version = null)
    {
        $this->ruleEngineName = $ruleEngineName ?? 'default_rule_engine';
        $this->latestVersion = $version ?? $this->loadRuleEngineVersion($version);
        $this->loadRules();
    }

    public function addRule(Rule $rule)
    {
        $fileName = "rule_json_data/".$this->ruleEngineName."_mapping_v".$this->latestVersion;
        $json = json_decode(file_get_contents($fileName), true);
        $json = $json ?? [];
        $rulePresent = false;
        foreach ($json as $data) {
            if($data['rule_engine_id'] == $this->id && $data['rule_id'] == $rule->id) {
                $rulePresent = true;
            }
        }
        if(!$rulePresent) {
            $json []= ['rule_engine_id' => $this->id, 'rule_id' => $rule->id];
            file_put_contents($fileName, json_encode($json));
            $this->rules[] = $rule;
        }
    }

    //TODO
    public function deleteRule(Rule $rule) {
    }

    public function loadRuleEngineVersion(){
        if (!file_exists(self::FILE_NAME)) {
            fopen(self::FILE_NAME, 'w');
        }
        $json = json_decode(file_get_contents(self::FILE_NAME), true);
        $id = 0;
        foreach((array)$json as $id => $data) {
            if ($data['name'] == $this->ruleEngineName){
                $this->id = $id;
                return $data['latest_version'];
            }
        }
        if (!isset($this->latestVersion)) {
            $id++;
            $json[$id] = ['name' => $this->ruleEngineName, 'latest_version' => 1];
            file_put_contents(self::FILE_NAME, json_encode($json));
            return 1;
        }
    }

    private function loadRules()
    {
        $fileName = "rule_json_data/".$this->ruleEngineName."_mapping_v".$this->latestVersion;
        if (!file_exists($fileName)) {
            fopen($fileName, 'w');
        }
        $json = json_decode(file_get_contents($fileName), true);
        foreach((array)$json as $data) {
            if ($data['rule_engine_id'] == $this->id){
                $rule = Rule::find($data['rule_id']);
                $this->rules[] =$rule;
            }
        }

    }

    public function getRules()
    {
        return $this->rules;
    }

    public function evaluateData($data)
    {
        $rulesSatisfied = true;
        $failedRules = [];
        foreach($this->rules as $rule) {
            $success = $rule->evaluate($data);
            if (!$success) {
                $failedRules[] = $rule;
            }
            $rulesSatisfied &= $success;
        }
        return ['success' => $rulesSatisfied, 'failed_rules' => $failedRules];
    }
}