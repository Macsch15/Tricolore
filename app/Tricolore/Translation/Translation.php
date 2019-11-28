<?php
namespace Tricolore\Translation;

use Tricolore\Application;
use Tricolore\Config\Config;
use Tricolore\Exception\AssetNotFound;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\XliffFileLoader;

class Translation
{
    /**
     * Get translator
     * 
     * @param string $resource
     * @throws Tricolore\Exception\AssetNotFound
     * @return Symfony\Component\Translation\Translator
     */
    public function getTranslator($resource = null)
    {
        $locale = Config::key('trans.locale');

        $translator = new Translator($locale, new MessageSelector());
        $translator->addLoader('xliff', new XliffFileLoader());

        if ($resource === null) {
            $translator->addResource('xliff', 
                Application::getInstance()->createPath(
                    sprintf('app:Tricolore:Translation:Resources:%s:messages.xliff', $locale)), 
                $locale);           
        } else {
            if (file_exists($resource) === false) {
                throw new AssetNotFound(sprintf('Translation resource "%s" not found.', $resource));
            }

            $translator->addResource('xliff', $resource, $locale);
        }

        $this->addValidatorResource($translator, $locale);

        $translator->setFallbackLocale(['en_EN']);

        return $translator;
    }

    /**
     * Add validator resource
     * 
     * @param Symfony\Component\Translation\Translator $translator
     * @param string $locale
     * @return void
     */
    private function addValidatorResource(Translator $translator, $locale)
    {
        $translator->addResource('xliff', 
            Application::getInstance()->createPath(
                sprintf('app:Tricolore:Translation:Resources:%s:validators.xliff', $locale)), 
            $locale);  
    }
}
