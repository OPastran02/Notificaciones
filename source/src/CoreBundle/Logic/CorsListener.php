<?php
namespace CoreBundle\Logic;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CorsListener
{
    protected $cors;

    public function __construct($cors = '*')
    {
        $this->cors = $cors;
    }
    public function onKernelResponse(FilterResponseEvent $event)
    {   
        $responseHeaders = $event->getResponse()->headers;

        /*$responseHeaders->set('Access-Control-Allow-Headers', 'origin, content-type, accept');
        $responseHeaders->set('Access-Control-Allow-Origin', '*');
        $responseHeaders->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');*/
        $responseHeaders->set("X-Content-Type-Options", "nosniff");
        $responseHeaders->set("X-Frame-Options", "DENY");
        $responseHeaders->set("X-XSS-Protection", "1");
        $responseHeaders->set("Access-Control-Allow-Origin",$this->cors);


    }   
}
