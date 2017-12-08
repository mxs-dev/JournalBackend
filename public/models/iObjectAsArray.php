<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 07.11.2017
 * Time: 10:50
 */

namespace app\models;


interface iObjectAsArray
{
    /*
     * returns object properties os array for sending via API.
     */
    public function getAsArray () ;
}