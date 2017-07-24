<?php
namespace BotShell\Controller;

interface ControllerInterface
{

    public function handle($parent, $arguments);
}