<?php

    function autoload_classes_outside_admin ($class_name)
    {
        @include_once './class/' . $class_name . '.class.php';
    }

    function autoload_admin_classes ($class_name)
    {
        @include_once './../class/' . $class_name . '.class.php';
    }

    function autoload_admin_classes_ajax ($class_name)
    {
        @include_once './../../class/' . $class_name . '.class.php';
    }

    spl_autoload_register('autoload_admin_classes');
    spl_autoload_register('autoload_classes_outside_admin');
    spl_autoload_register('autoload_admin_classes_ajax');