<?php
// src/OpenSkedge/AppBundle/Twig/Extension/PageExtension.php

namespace OpenSkedge\AppBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;


class PageExtension extends \Twig_Extension
{
    protected $request;
    /**
     *
     * @var \Twig_Environment
     */
    protected $environment;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
        return array(
                'get_controller_name' => new \Twig_Function_Method($this, 'getControllerName'),
                'get_action_name' => new \Twig_Function_Method($this, 'getActionName'),
        );
    }

    /**
     * Get current controller name
     */
    public function getControllerName()
    {
        $pattern = "#Controller\\\([a-zA-Z]*)Controller#";
        $matches = array();
        preg_match($pattern, $this->request->get('_controller'), $matches);

        return strtolower($matches[1]);
    }

    /**
     * Get current action name
     */
    public function getActionName()
    {
        $pattern = "#::([a-zA-Z]*)Action#";
        $matches = array();
        preg_match($pattern, $this->request->get('_controller'), $matches);

        return $matches[1];
    }

    public function getName()
    {
        return 'page';
    }
}
