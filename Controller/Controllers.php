<?php
namespace BotShell\Controller;

use BotShell\Controller\ControllerInterface;
use BotShell\Request\RequestInterface;
use BotShell\Response\ResponseInterface;

class Controllers implements ControllerInterface
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
        call_user_func_array([$this, 'controllers'], $arguments);
        return $parent;
    }

    public function controllers($routers)
    {
        foreach ($routers as $route => $callback) {
            if ($this->request->getRoute() == $route) {
                if (is_object($callback)) {
                    $callback->handler($this->request, $this->response);
                } else if ($callback instanceof \Closure) {
                    $callback($this->request, $this->response);
                } else if (is_string($callback)) {
                    echo $callback;
                }
            }
        }
        return $this;
    }

}