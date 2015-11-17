<?php

namespace LoftDigital\RestBundle\Services;

/**
 * Class FormHelper
 *
 * @author George Mylonas <georgem@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\RestBundle\Services
 */
class FormHelper
{
    /**
     * Takes care of $form and return custom error array with errors and messages
     *
     * @param $form
     *
     * @return array
     */
    public function getErrors($form)
    {
        $errors = array();
        foreach ($form as $fieldName => $formField) {
            $currentError = $formField->getErrors();

            if ($currentError->current()) {
                $current = $currentError->current();
                $errors[$fieldName] = $current->getMessage();
            }
        }

        return $errors;
    }

    /**
     * Returns form errors as string, appending field names to message
     *
     * @param $message
     * @param $form
     *
     * @return string
     */
    public function getErrorsAsString($form, $message = "Invalid parameters ")
    {
        $errors = $this->getErrors($form);

        foreach (array_keys($errors) as $field) {
            $message .= $field . ', ';
        }

        $message = substr($message, 0, -2);

        return $message;
    }
}
