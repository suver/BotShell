<?php
namespace BotShell\Controller;

use BotShell\Controller\ControllerInterface;
use BotShell\Request\RequestInterface;
use BotShell\Response\ResponseInterface;

class Controller implements ControllerInterface
{

    private $request;

    private $response;

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function handle($parent, $arguments)
    {
        call_user_func_array([$this, 'controller'], $arguments);
        return $parent;
    }

    public function controller($route, \Closure $callback)
    {
        if ($this->request->getRoute() == $route) {
            $callback($this->request, $this->response);
        }
        return $this;
    }

}