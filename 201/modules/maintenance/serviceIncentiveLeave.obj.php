<?php

    class serviceIncentiveLeaveObj extends commonObj {
        var $get;//method
        var $session;//session variables
        /**
         * pass all the get variables and session variables 
         *
         * @param string $method
         * @param array variable  $sessionVars
         */
        function __construct($method,$sessionVars){
            $this->get = $method;
            $this->session = $sessionVars;
        }
    }