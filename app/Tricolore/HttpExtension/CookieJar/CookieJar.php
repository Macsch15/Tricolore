<?php
namespace Tricolore\HttpExtension\CookieJar;

use Tricolore\Config\Config;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CookieJar
{
    /**
     * Response
     * 
     * @var Symfony\Component\HttpFoundation\Response
     */
    private $response;

    /**
     * Request
     * 
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * Construct
     *  
     * @return void
     */
    public function __construct()
    {
        $this->response = new Response();
        $this->request = Request::createFromGlobals();
    }

    /**
     * Set new cookie
     * 
     * @param string $name
     * @param string $value 
     * @param int $expire
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function set($name, $value, $expire = 86400)
    {
        $this->response->headers->setCookie(
            new Cookie($name, 
                $value, 
                Carbon::now()->addSeconds($expire)->timestamp, 
                Config::key('cookie.path'), 
                Config::key('cookie.domain'), 
                Config::key('cookie.secure')));

        return $this->response->send();
    }

    /**
     * Get existing cookie
     * 
     * @param string $name
     * @return string|bool
     */
    public function get($name)
    {
        if ($this->request->cookies->get($name) !== null) {
            return trim(str_replace(["\0", "\n", "\t", "\s"], '', $this->request->cookies->get($name)));
        }

        return false;
    }

    /**
     * Destroy cookie
     * 
     * @param string $name
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function destroy($name)
    {
        if ($this->request->cookies->get($name) !== null) {
            $this->request->cookies->remove($name);
        }

        $this->response->headers->setCookie(
            new Cookie($name, 
                null, 
                -1, 
                Config::key('cookie.path'), 
                Config::key('cookie.domain'), 
                Config::key('cookie.secure')));

        return $this->response->send();
    }
}
