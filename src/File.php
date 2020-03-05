<?php
/**
 * Created by SERJIK
 */

namespace Twom\FileManager;

use Twom\FileManager\BaseType;

class File
{
    /**
     * select type
     *
     * @param null $type
     * @return \Twom\FileManager\BaseType
     */
    public function type($type = null)
    {
        if (is_null($type))
            $type = config("filemanager.type");

        $config = filemanager_config($type);

        /** @var BaseType $providerClass */
        $providerClass = $config['provider'];
        $providerClass = new $providerClass;
        $providerClass->setType($type)
            ->setConfig($config)
            ->fetchProperties();
        return $providerClass;
    }


    public function __call($method, $parameters)
    {
        return $this->type()->$method(...$parameters);
    }
}
