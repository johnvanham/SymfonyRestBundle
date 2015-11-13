<?php

namespace LoftDigital\RestBundle\Services;

/**
 * Class FormHelper
 *
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
            // each field has an array of errors
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
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getErrorsAsString($form, $message = "Invalid parameters ")
    {
        $errors = $this->getErrors($form);

        foreach ($errors as $field => $error) {
            $message .= $field . ', ';
        }

        $message = substr($message, 0, -2);

        return $message;
    }
}
