<?php
namespace BotShell\Controller;

use BotShell\Request\RequestInterface;
use BotShell\Response\ResponseInterface;

class Chain implements ControllerInterface
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
        call_user_func_array([$this, 'chain'], $arguments);
        return $parent;
    }

    public function chain($callback)
    {
        $callback($this->request, $this->response);
        return $this;
    }

}