<?php
namespace Tricolore\View;

use Tricolore\Application;
use Tricolore\Config\Config;
use Tricolore\Services\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Carbon\Carbon;

class View extends ServiceLocator
{
    /**
     * Twig Environment
     *  
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * Integrate with Twig
     * 
     * @param bool $safe_mode
     * @return Tricolore\View
     */
    public function register($safe_mode = false)
    {
        \Twig_Autoloader::register();

        $finder = $this->get('finder')
        ->directories()
        ->in(Application::createPath('app:Tricolore:View:Templates'));

        foreach ($finder as $file) {
            $directories[] = $file->getRealpath();
        }

        $directories = array_merge($directories, [
            Application::createPath('app:Tricolore:View:Templates')
        ]);

        if (Application::getInstance()->getEnv() === 'test') {
            $directories = array_merge($directories, [
                Application::createPath('app:Tricolore:Tests:Fixtures:Templates') 
            ]);
        }

        $loader = new \Twig_Loader_Filesystem($directories);

        $in_prod = Application::getInstance()->getEnv() === 'prod';

        $this->environment = new \Twig_Environment($loader, [
            'cache' => ($in_prod === true) ? Application::createPath(Config::key('directory.storage') . ':twig') : false,
            'auto_reload' => ($in_prod === true) ? false : true,
            'strict_variables' => ($in_prod === true) ? false : true
        ]);

        $this->registerGlobals();
        $this->registerFunctions();

        if ($safe_mode === false) {
            $this->formIntegration();
            $this->transIntegration();
        }

        return $this;
    }

    /**
     * Render and display
     * 
     * @param string $template_section
     * @param string $template_name
     * @param array $variables
     * @param bool $return
     * @return mixed
     */
    public function display($template_section, $template_name, array $variables = [], $return = false)
    {
        if (endsWith('.html.twig', $template_name, 10) === false) {
            $template_name .= '.html.twig';
        }

        $combined_template_path = ($template_section != null ? $template_section . '/' : null) . $template_name;

        if (Config::key('gzip.enabled') === true && extension_loaded('zlib') === true) {
            ob_start('ob_gzhandler');

            if ($return === true) {
                $this->environment->loadTemplate($combined_template_path)->render($variables);
            } else {
                $this->environment->loadTemplate($combined_template_path)->display($variables);
            }

            header('Connection: close');

            ob_end_flush();

            return true;
        }

        if ($return === true) {
            return $this->environment->loadTemplate($combined_template_path)->render($variables);
        }

        return $this->environment->loadTemplate($combined_template_path)->display($variables);
    }

    /**
     * Register global variables
     * 
     * @return void
     */
    private function registerGlobals()
    {
        $this->environment->addGlobal('app', Application::getInstance());
    }

    /**
     * Register functions
     *  
     * @return void
     */
    private function registerFunctions()
    {
        $this->environment->addFunction(new \Twig_SimpleFunction('config', function ($key) {
            return Config::key($key);
        }));

        $this->environment->addFunction(new \Twig_SimpleFunction('assets', function ($section, $file) {
            return Config::key('base.full_url') . '/' . Config::key('directory.assets') . '/' . $section . '/' . $file;
        }));

        $this->environment->addFunction(new \Twig_SimpleFunction('url', function ($route_name = null, $arguments = []) {
            return Application::getInstance()->buildUrl($route_name, $arguments);
        }));
    }

    /**
     * Form integration
     * 
     * @return void
     */
    private function formIntegration()
    {
        $form = new TwigRendererEngine(['bootstrap_3_layout.html.twig']);
        $form->setEnvironment($this->environment);

        $this->environment->addExtension(new FormExtension(new TwigRenderer($form)));        
    }

    /**
     * Translation integration
     * 
     * @return void
     */
    private function transIntegration()
    {
        if (Application::getInstance()->getEnv() === 'test') {
            $this->environment->addExtension(new TranslationExtension($this->get('translator', [
                Application::createPath('app:Tricolore:Tests:Fixtures:Translation_enEN.xliff'),
                'en_EN'
            ])));
        } else {
            $this->environment->addExtension(new TranslationExtension($this->get('translator')));
        }
    }

    /**
     * Accessor for \Twig_Environment
     * 
     * @return \Twig_Environment
     */
    public function getEnv()
    {
        return $this->environment;
    }

    /**
     * Handle exception
     * 
     * @param \Exception $exception 
     * @param bool $return
     * @return void
     */
    public function handleException($exception, $return = false)
    {
        http_response_code(500);

        $reflection = new \ReflectionClass(get_class($exception));

        if ($reflection->getName() !== 'Tricolore\Exception\ErrorException') {
            $this->logException($exception);
        }

        if (Application::getInstance()->getEnv() === 'prod') {
            return $this->display('Exceptions', 'HandleClientException');
        }

        $error_file = $exception->getFile();
        $error_line = $exception->getLine();

        if ($reflection->getName() === 'Tricolore\Exception\ErrorException') {
            $error_file = $exception->getErrorFile();
            $error_line = $exception->getErrorLine();
        }

        $file_array = new \SplFileObject($error_file, 'r');

        $request = Request::createFromGlobals();

        return $this->display('Exceptions', 'HandleDevException', [
            'exception' => $exception,
            'file_array' => iterator_to_array($file_array),
            'error_line' => $error_line,
            'error_file' => $error_file,
            'exception_name' => $reflection->getShortName(),
            'path_info' => $request->getPathInfo()
        ], $return);
    }

    /**
     * Log exception
     * 
     * @param \Exception $exception 
     * @return void
     */
    private function logException($exception)
    {
        $filesystem = new Filesystem();

        $exception_log = str_repeat('-', 20) . ' LAST EXCEPTION LOG ' . str_repeat('-', 20) . PHP_EOL . PHP_EOL;
        $exception_log .= 'MESSAGE: ' . $exception->getMessage() . PHP_EOL;
        $exception_log .= 'FILE: ' . $exception->getFile() . PHP_EOL;
        $exception_log .= 'LINE: ' . $exception->getLine() . PHP_EOL;
        $exception_log .= 'TIME: ' . Carbon::now()->toDateTimeString() . PHP_EOL . PHP_EOL;
        $exception_log .= str_repeat('-', 20) . ' LAST EXCEPTION LOG ' . str_repeat('-', 20);

        if (Application::getInstance()->getEnv() !== 'test') {
            $filesystem->dumpFile(Application::createPath(Config::key('directory.storage') . ':last_exception.txt'), $exception_log);
        }
    }
}
