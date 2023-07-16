<?php

namespace App\Component;
use Symfony\Component\Yaml\Yaml;

/**
 * Description of ConfigurationHandler
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
final class ConfigurationHandler {

    private string $re = "/(\.yml|\.yaml)$/";

    public function setEnviromentDataFromConfig(): void {
        $dir = 'config';
        $configArray = scandir($dir);
        foreach ($configArray as $configFile) {
            if (preg_match($this->re, $configFile)) {
                $mysqlparameters = Yaml::parseFile($dir . '/' . $configFile);
                foreach($mysqlparameters["connection"] as $key => $value){
                    putenv($key.'='.$value);
                }
                foreach($mysqlparameters["setup"] as $key => $value){
                    putenv($key.'='.$value);
                }
            }
        }
    }
    
    
}
