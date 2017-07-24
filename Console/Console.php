<?php
namespace BotShell\Console;

use BotShell\BotShell;
use BotShell\Controller\ControllerInterface;
use BotShell\Request\RequestInterface;
use BotShell\Response\ResponseInterface;
use BotShell\ShellInterface;

class Console implements ShellInterface
{

    private $request;

    private $response;

    private $controllers = [];

    private static $instance;

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->addController(BotShell::injection()->get('\BotShell\Controller\Chain'));
        $this->addController(BotShell::injection()->get('\BotShell\Controller\Controller'));
        $this->addController(BotShell::injection()->get('\BotShell\Controller\Controllers'));
        $this->addController(BotShell::injection()->get('\BotShell\Controller\Form'));

        $this->request = $request;
        $this->response = $response;
    }

    public function __call($name, $arguments)
    {
        if (array_key_exists(strtolower($name), $this->controllers)) {
            return $this->controllers[$name]->handle($this, $arguments);
        }
    }

    public function addController(ControllerInterface $controllerObject)
    {
        $name = basename(str_replace('\\', '/', get_class($controllerObject)));
        $this->controllers[strtolower($name)] = $controllerObject;
    }

    public function run()
    {
        if (empty(self::$instance)) {
            self::$instance = BotShell::injection()->get('\BotShell\Console\Console');
        }
        return self::$instance;
    }

    public function requestOptions(array $options = [])
    {
        $this->request->setOptions($options);
        return $this;
    }


}