<?php

namespace Models;

class Rule
{
    const LESS_THAN = "<";
    const HIGH = "HIGH";
    const LOW = "LOW";
    const SHOULD_NOT_RISE_ABOVE = "<";
    const SHOULD_NEVER_BE = "<>";
    const SHOULD_NOT_BE= "!=";
    const SHOULD_BE = "==";
    const GREATER_THAN = ">";
    const FUTURE = "future";
    const PAST = "past";

    const FILE_NAME = 'rule_json_data/rules.json';
    const SPECIAL_EXPRESSIONS = [
        self::FUTURE,
        self::PAST,
        self::LOW,
        self::HIGH
    ];

    private $variable, $value, $operand;
    public $id;

    /**
     * Rule constructor.
     * @param $variable
     */
    public function __construct($variable, $operand, $value)
    {
        $this->variable = $variable;
        $this->operand = $operand;
        $this->value = $value;
        $this->id = $this->save();
    }

    public function save(){
        $fileName = self::FILE_NAME;
        if (!file_exists($fileName)) {
            fopen($fileName, 'w');
        }
        $json = json_decode(file_get_contents($fileName), true);
        $id = 0;
        foreach((array)$json as $id => $data) {
            if ($data['variable'] == $this->variable && $data['operand'] == $this->operand && $data['value'] == $this->value){
                return $id;
            }
        }
        $id++;
        $json[$id] = ['variable' => $this->variable, 'operand' => $this->operand, 'value' => $this->value];
        file_put_contents($fileName, json_encode($json));
        return $id;
    }

    public static function find($id)
    {
        $fileName = self::FILE_NAME;
        $json = json_decode(file_get_contents($fileName), true);
        $data = isset($json[$id]) ? $json[$id] : null;
        return isset($data) ? new self($data['variable'], $data['operand'], $data['value']): null;
    }

    public function __toString()
    {
        return $this->variable.' '.$this->operand.' '.$this->value;
    }

    public function evaluate($data)
    {
        return $data['signal'] != $this->variable || $this->evaluateExpression($data['parsed_value'], $this->value, $this->operand);
    }

    private function evaluateExpression($givenValue, $expectedValue, $operand)
    {
        if (in_array($expectedValue, self::SPECIAL_EXPRESSIONS)) {
            $expectedValue = $this->getDataFromSpecialExpression($expectedValue, $givenValue);
        }
        $result = null;
        eval('$result = '.$givenValue.$operand.$expectedValue.';');
        return $result;
    }

    private function getDataFromSpecialExpression($expression, $givenValue)
    {
        $response = null;
        switch ($expression)
        {
            case self::FUTURE:
                $response = $givenValue > strtotime('now') ? 'true' : 'false';
                break;
            case self::PAST:
                $response = $givenValue < strtotime('now') ? 'true' : 'false';
                break;
            case self::LOW:
                $response = '"LOW"';
                break;
            case self::HIGH:
                $response = '"HIGH"';
                break;
        }
        return $response;
    }
}