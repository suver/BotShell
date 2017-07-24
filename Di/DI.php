<?php
namespace BotShell\Di;


class DI
{
    private static $context;

    /**
     * @var array singleton objects indexed by their types
     */
    private $_singletons = [];

    private $injections = [];

    public function __construct()
    {
        self::$context = new Context();
        $this->addInjection(new InjectionClass());
        $this->addInjection(new InjectionVariable());
        ClassRepository::init();

    }

    public function addInjection($injection)
    {
        $this->injections[$injection->type] = $injection;
    }

    public function getInjection($type)
    {
        return $this->injections[$type];
    }

    /**
     * @return InjectionClass
     */
    public function set($class, $singleton = false)
    {
        if ($singleton) {
            $this->_singletons[$class] = null;
        } else {
            unset($this->_singletons[$class]);
        }
        return (new InjectionClass())->set($class);
    }

    /**
     * @return InjectionVariable
     */
    public function setVariable($class, $singleton = false)
    {
        if ($singleton) {
            $this->_singletons[$this->normalizeName($class)] = null;
        } else {
            unset($this->_singletons[$this->normalizeName($class)]);
        }
        return (new InjectionVariable())->set($class);
    }

    public function execute($reflection, $dependencies)
    {
        if (!$reflection->isInstantiable()) {
            throw new \Exception("Can not instantiate {$reflection->name}");
        }
        return $reflection->newInstanceArgs($dependencies);
    }

    public function get()
    {
        $values = func_get_args();
        $class = array_shift($values);

        if (!empty($this->_singletons[$this->normalizeName($class)])) {
            return $this->_singletons[$this->normalizeName($class)];
        }

        list ($reflection, $dependencies) = self::$context->getDependencies($class);
        $dependencies = $this->resolveDependencies($dependencies, $reflection);
        $object = $this->execute($reflection, $dependencies);
        if ($this->_singletons[$this->normalizeName($class)] === null) {
            $this->_singletons[$this->normalizeName($class)] = $object;
        }
        return $object;
    }

    /**
     * Resolves dependencies by replacing them with the actual object instances.
     * @param array $dependencies the dependencies
     * @param \ReflectionClass $reflection the class reflection associated with the dependencies
     * @return array the resolved dependencies
     * @throws \Exception if a dependency cannot be resolved or if a dependency cannot be fulfilled.
     */
    protected function resolveDependencies($dependencies, $reflection = null)
    {
        foreach ($dependencies as $index => $dependency) {
            if ($dependency instanceof Instance) {
                if ($dependency->dependency !== null) {
                    $dependencies[$index] = $this->get($dependency->dependency);
                } elseif ($reflection !== null) {
//                    var_dump($index, $dependency);
                }
            }
        }
        return $dependencies;
    }

    private function normalizeName($name)
    {
        $name = basename(str_replace('\\', '/', $name));
        return $name;
    }

}