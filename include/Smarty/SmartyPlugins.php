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
     * Helper function for
     *
     * @param $params
     * @return string
     */
    public function menuColorBackground($params) {
        $colors = sscanf($params, '#%02x%02x%02x');
        if (in_array(null, $colors)) {
            return $params;
        }

        $colors[0] += (255 - $colors[0]) * 0.45;
        $colors[1] += (255 - $colors[1]) * 0.45;
        $colors[2] += (255 - $colors[2]) * 0.45;

        foreach ($colors as &$color) {
            $color = min(max($color, 0), 255);
        }

        return vsprintf('#%02x%02x%02x', $colors);
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

