<?php

namespace Simpler\Http\Controllers;

use Simpler\Traits\WebhookValidation;
use WP_REST_Request;


abstract class Controller
{

    const SIMPLERWC_API_NAMESPACE = 'vendor/simpler/v1';

    use WebhookValidation;

    /**
     * Route namespace.
     *
     * @var string
     */
    protected $namespace = self::SIMPLERWC_API_NAMESPACE;

    /**
     * Route name.
     *
     * @var string
     */
    protected $route;

    /**
     * Route methods.
     *
     * @var string
     */
    protected $method;
    /**
     * Route permission callback.
     *
     * @var string
     */
    protected $permissionCallback = '__return_true';

    public function __construct()
    {
    }

    /**
     * Route handler function.
     *
     * @param  WP_REST_Request  $request  JSON request.
     */
    abstract public function handle($request);

    /**
     * @return string
     */
    public function get_route()
    {
        return $this->route;
    }

    /**
     * @return string
     */
    public function get_method()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function get_namespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function get_permission_callback()
    {
        return $this->permissionCallback;
    }

    public function WCBasicAuth()
    {
        return apply_filters( 'determine_current_user', false );
    }
}
