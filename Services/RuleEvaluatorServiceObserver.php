<?php

namespace Services;

interface RuleEvaluatorServiceObserver
{
    public function updateObserverResponse($data, $success, $failedRules);
}