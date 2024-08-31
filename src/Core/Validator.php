<?php

namespace App\Core;

class Validator
{
    protected array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $rule) {
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }

            foreach ($rule as $singleRule) {
                $this->applyRule($field, $singleRule, $data);
            }
        }

        return empty($this->errors);
    }

    protected function applyRule($field, $rule, $data): void
    {
        if (str_contains($rule, "##")) {
            list($rule, $sub_rule) = explode("##", $rule);
            $rule = str_replace("##", "", $rule);
        }
        switch ($rule) {
            case 'required':
                if (!isset($data[$field]) || $data[$field] === '') {
                    $this->addError($field, "$field is required");
                }
                break;
            case 'string':
                if (isset($data[$field]) && !is_string($data[$field])) {
                    $this->addError($field, "$field must be a string");
                }
                break;
            case 'email':
                if (isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "$field must be a valid email address");
                }
                break;
            case 'min':
                if (isset($data[$field]) && strlen($data[$field]) < $sub_rule) {
                    $this->addError($field, "$field must be a $sub_rule char long");
                }
                break;
        }
    }

    protected function addError($field, $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}