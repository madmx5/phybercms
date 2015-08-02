<?php defined('SYSPATH') OR die('No direct script access.');

class Validation extends Kohana_Validation {

    /**
     * Adds a rules to the object given an array of fields
     *
     *     $validation = new Validation($_POST);
     *     $validation->fields_rules(array('field1' => array(
     *             array('not_empty')
     *         )) );
     *
     * @param   array           Array of fields and rules for each field
     * @return  Validation
     * @chainable
     */
    public function fields_rules(array $fields_rules)
    {
        foreach ($fields_rules as $field => $rules)
        {
            $this->rules($field, $rules);
        }

        return $this;
    }
}

