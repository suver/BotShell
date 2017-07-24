<?php
namespace BotShell\Controller;

use BotShell\Request\RequestInterface;
use BotShell\Response\ResponseInterface;

class Form implements ControllerInterface
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
        call_user_func_array([$this, 'form'], $arguments);
        return $parent;
    }

    public function form($question, $callback)
    {
        $callback($this->request, $this->response);
        return $this;
    }

}