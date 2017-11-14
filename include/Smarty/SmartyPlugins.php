<?php

namespace Ecdb\Smarty;

class SmartyPlugins {

    protected $container;
    /**
     * SmartyPlugins constructor.
     * @param \Interop\Container\ContainerInterface $container
     */
    public function __construct($container) {
        $this->container = $container;
    }

    /**
     * Get url path by route name
     *
     * @param array $params
     * @param $smarty
     * @return string
     */
    public function pathFor($params, &$smarty) {
        if (empty($params['name'])) {
            return '';
        }

        $router = $this->container->get('router');
        try {
            $route_params = $params;
            unset($route_params['name']);
            $path = $router->pathFor($params['name'], $route_params);
        } catch (\Exception $e) {
            error_log('SmartyPlugins: ' . $e->getMessage());
        }
        return $path;
    }

    /**
     * Helper for Material Design icons
     *
     * @param array $params
     * @param $smarty
     * @return string
     */
    public function mdIcon($params, &$smarty) {
        if (empty($params['name'])) {
            return '';
        }
        if (empty($params['size'])) {
            return '';
        }

        $name = $params['name'];
        $size = $params['size'];

        return "<i class=\"material-icons\" style=\"font-size: {$size}px; width: {$size}px; height: {$size}px; vertical-align: bottom;\">$name</i>";
    }
}

