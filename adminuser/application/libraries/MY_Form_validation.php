<?php
/**
 * 
 * Enter description here ...
 * @return CI_Controller
 */
class MY_Form_validation extends CI_Form_validation
{
    public function error_array()
    {
        return $this->_error_array;
    }
}