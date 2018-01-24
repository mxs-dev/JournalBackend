<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 24.01.2018
 * Time: 15:29
 */

namespace app\sse;


class SseEvent
{
    /** @var  $data string */
    public $data;

    /** @var  $permissionName string */
    public $permissionName;

    /** @var  $permissionData array */
    public $permissionData;

    public function __construct ($data, $permissionName = null, $permissionData = [])
    {
        $this->data = $data;
        $this->permissionName = $permissionName;
        $this->permissionData = $permissionData;
    }
}