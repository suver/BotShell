<?php
namespace BotShell\Request;

class Request implements RequestInterface
{
    private $options;
    private $optionsConfigure = [];

    public function __construct()
    {
    }

    public function getOptions(array $optionsConfigure = [])
    {
        if (empty($this->options)) {
            $this->compileOptions($optionsConfigure);
        }
        return $this->options;
    }

    public function getRoute() {
//        var_dump($argv);
        return isset($this->options['route']) ? $this->options['route'] : null;
    }

    public function setOptions(array $optionsConfigure = [])
    {
        $this->compileOptions($optionsConfigure);
        return $this->options;
    }

    private function compileOptions(array $optionsConfigure = [])
    {
        // Set the default values.
        $optionsConfigure = array_merge_recursive([
            'params' => '',
            'os' => '',
            'username' => posix_getpwuid(posix_geteuid())['name'],
            'env' => ''
        ], $optionsConfigure);

        $params = [];
        $_optionsConfigure = [];
        $defaults = [];
        foreach ($optionsConfigure as $option => $parameters) {
            $option = is_numeric($option) ? $parameters : $option;
            $require = is_array($parameters) && isset($parameters['require']) && $parameters['require'] ? true : false;
            $require = is_string($parameters) ? true : $require;
            $boolean = is_array($parameters) && isset($parameters['boolean']) && $parameters['boolean'] ? true : false;
            $boolean = is_bool($parameters) ? true : $boolean;
            $type = is_array($parameters) && isset($parameters['type']) ? $parameters['type'] : 'string';
            switch ($type) {
                case "string":
                case "str":
                case "s":
                    $type = 'string';
                    break;
                case "numeric":
                case "number":
                case "float":
                case "integer":
                case "int":
                case "i":
                    $type = 'integer';
                    break;
                case "boolean":
                case "bool":
                case "b":
                    $type = 'boolean';
                    $boolean = true;
                    break;
            }
            $default = is_array($parameters) && isset($parameters['default']) ? $parameters['default'] : null;
            $default = is_string($parameters) ? $parameters : $default;

            $params[] = $option . ($boolean ? "::" : ":");
            $_optionsConfigure[$option] = [
                'name' => $option,
                'require' => $require,
                'boolean' => $boolean,
                'type' => $type,
                'default' => $default,
            ];
            $defaults[$option] = $default;
        }
        $optionsConfigure = $_optionsConfigure;
        unset($_optionsConfigure);

        // Sufficient enough check for CLI.
        if ('cli' === PHP_SAPI) {
            $options = getopt('', $params);
            foreach ($optionsConfigure as $option => $parameters) {
                if (isset($options[$option]) && $parameters['boolean']) {
                    $options[$option] = true;
                } else if (!isset($options[$option]) && $parameters['boolean']) {
                    $options[$option] = false;
                } else if (!isset($options[$option]) && $parameters['require']) {
                    $options[$option] = $parameters['default'];
                }
            }
            $this->options = $options;
            return $this->options;
        }

        $this->options = $_GET + $defaults;
        return $this->options;
    }


}