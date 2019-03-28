<?php

namespace Desidus\Rudder;

class Validator
{
    private $errors;
    private $request;

    public function __construct($rules)
    {
        $this->validateRules($rules);
    }

    public static function make($rules)
    {
        return new self($rules);
    }

    private function validateRules($rules)
    {
        $this->errors = [];
        $this->request = App::request();

        foreach ($rules as $ruleName => $rulesValue) 
        {
            $value = $this->request->input($ruleName);
            $rulesValue = explode('|', $rulesValue);

            if (in_array('required', $rulesValue)) 
            {
                if ($value == null)
                {
                    $this->addError("Questo campo è obbligatorio.", $ruleName);
                    continue;
                }

                unset($rules[array_search('required', $rules)]);
            }

            foreach ($rulesValue as $rule) 
            {
                $this->validateRule($ruleName, $rule, $value);
            }
        }

        $this->sanitizeErrors();
    }

    private function validateRule($ruleName, $rule, $value)
    {
        $rule = explode(':', $rule);

        $name = $rule[0];
        $arguments = $name == 'regex' ? $rule[1] : (isset($rule[1]) ? explode(',', $rule[1]) : []);

        switch($name)
        {
            case "email":
                if (!preg_match('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix', $value)) 
                    $this->addError("L'email non è valida.", $ruleName);
                break;
            case "in":
                if (!in_array($value, $arguments)) 
                    $this->addError("Questo campo non è compreso tra i valori specificati.", $ruleName);
                break;
            case "file":
                if (!$this->request->hasFile($ruleName))
                    $this->addError("Questo campo non è un file.", $ruleName);
                break;
            case "size":
                if($this->request->hasFile($ruleName))
                {
                    if ($_FILES[$ruleName]['size'] > $arguments[0])
                        $this->addError("Dimensione di caricamento superata.", $ruleName);
                    continue;
                }
                else if(is_string($value)) 
                {
                    if (strlen($value) > $arguments[0])
                        $this->addError("Lunghezza masssima superata.", $ruleName);
                } 
                else if(is_array($value)) 
                {
                    if (count($value) > $arguments[0])
                        $this->addError("Numero massimo di elementi superati.", $ruleName);
                }
                break;
            case "mime":
                if ($this->request->hasFile($ruleName))
                {
                    $mime = mime_content_type($value['tmp_name']);
                    if (!in_array($mime, $arguments))
                        $this->addError("Questo tipo di file non è supportato.", $ruleName);
                }
                break;
            case "mime_ext":
                if ($this->request->hasFile($ruleName))
                {
                    $ext = pathinfo($value['name'], PATHINFO_EXTENSION);
                    if (!in_array($ext, $arguments))
                        $this->addError("Questa estensione non è supportata.", $ruleName);
                }
                break;
            case "regex":
                if (!preg_match($arguments, $value)) 
                    $this->addError("Questo campo contiene caratteri non validi.", $ruleName);
                break;
        }
    }

    private function addError($error, $key)
    {
        array_key_exists($key, $this->errors) ? array_push($this->errors[$key], $error) : $this->errors[$key] = [$error];
    }


    private function sanitizeErrors()
    {
        foreach ($this->errors as $key => $value)
            if (count($value) == 1)
                $this->errors[$key] = $value[0];
    }

    public function isValid()
    {
        return count($this->errors) === 0;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}