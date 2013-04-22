<?php

namespace OpenSkedge\AppBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;


class SettingsExtension extends \Twig_Extension
{
    /**
     *
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     *
     * @var \OpenSkedge\AppBundle\Entity\Settings
     */
    protected $appSettings;

    public function __construct(\OpenSkedge\AppBundle\Services\AppSettingsService $appSettingsService)
    {
        $this->appSettings = $appSettingsService->getAppSettings();
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
        return array(
            'app_brand_name' => new \Twig_Function_Method($this, 'getBrandName'),
            'start_time' => new \Twig_Function_Method($this, 'getStartHour'),
            'end_time' => new \Twig_Function_Method($this, 'getEndHour'),
            'default_time_res' => new \Twig_Function_Method($this, 'getTimeResolution'),
        );
    }

    public function getBrandName()
    {
        return $this->appSettings->getBrandName();
    }

    public function getTimeResolution()
    {
        return $this->appSettings->getDefaultTimeResolution();
    }

    public function getStartHour()
    {
        return $this->appSettings->getStartHour();
    }

    public function getEndHour()
    {
        return $this->appSettings->getEndHour();
    }

    public function getName()
    {
        return 'app_settings';
    }
}
