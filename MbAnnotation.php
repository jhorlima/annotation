<?php

namespace MbAnnotation;

use Carbon\Carbon;
use MbAnnotation\standard\MbAction;
use MbAnnotation\standard\MbAsset;
use MbAnnotation\standard\MbPage;
use MbAnnotation\standard\MbShortCode;
use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;
use MocaBonita\MocaBonita;
use MocaBonita\tools\MbException;
use MocaBonita\tools\MbPath;
use MocaBonita\tools\MbSingleton;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Class MbAnnotation
 *
 * @package MbAnnotation
 **/
class MbAnnotation extends MbSingleton
{

    /**
     * @var bool
     *
     * */
    protected $ignoreCache = false;

    /**
     * @return bool
     */
    public function isIgnoreCache()
    {
        return $this->ignoreCache;
    }

    /**
     * @param bool $ignoreCache
     *
     * @return MbAnnotation
     */
    public function setIgnoreCache($ignoreCache)
    {
        $this->ignoreCache = $ignoreCache;

        if($ignoreCache){
            Annotations::$config['cache'] = false;
        }

        return $this;
    }

    /**
     * @param string     $controller
     *
     * @param MocaBonita $mocaBonita
     *
     * @return $this
     *
     * @throws MbException
     *
     **/
    public function mbPage($controller, MocaBonita $mocaBonita = null)
    {
        if (!$this->ignoreCache && !file_exists(MbPath::pDir("/cache"))) {
            throw new MbException("The '" . MbPath::pDir("/cache") . "' cache directory does not exist!");
        }

        $cache = new FilesystemAdapter('annotation', 86400, MbPath::pDir("/cache"));

        $controllerCache = $cache->getItem(md5($controller));

        if (!$controllerCache->isHit() || $this->ignoreCache) {

            $pages = Annotations::ofClass($controller, '@page');

            if (empty($pages)) {
                throw new MbException("No '@page' attribute found in Class");
            }

            $page = $pages[0];

            $assets = Annotations::ofClass($controller, '@asset');

            $methods = [];

            foreach (get_class_methods($controller) as $method) {
                $methods[$method] = [
                    'action'    => Annotations::ofMethod($controller, $method, '@action'),
                    'shortcode' => Annotations::ofMethod($controller, $method, '@shortcode'),
                    'assets'    => Annotations::ofMethod($controller, $method, '@asset'),
                ];
            }

            foreach ($methods as $name => $method) {
                if (empty($method['action']) && empty($method['shortcode'])) {
                    unset($methods[$name]);
                }
            }

            $data = [
                'page'    => $page,
                'assets'  => $assets,
                'methods' => $methods,
            ];

            $expireAt = new Carbon();
            $expireAt->addWeek(3);

            $controllerCache->expiresAt($expireAt);
            $controllerCache->set($data);

            $cache->save($controllerCache);

        } else {
            $data = $controllerCache->get();
        }

        $mbPage = $data['page']->mocaBonita($mocaBonita, $controller);

        foreach ($data['assets'] as $asset) {
            $asset->mocaBonita($mbPage->getMbAsset());
        }

        foreach ($data['methods'] as $name => $method) {
            if (!empty($method['action'])) {
                $method['action'][0]->mocaBonita($mbPage, $name);
            } else {
                $shortcode = $method['shortcode'][0]->mocaBonita($mocaBonita, $mbPage, $name);

                foreach ($method['assets'] as $asset) {
                    $asset->mocaBonita($shortcode->getMbAsset());
                }
            }
        }

        return $this;
    }

    /**
     * Init
     */
    public function init()
    {

        Annotations::$config['cache'] = new AnnotationCache(MbPath::pDir('/cache'));

        Annotations::getManager()->registry['page'] = MbPage::class;

        Annotations::getManager()->registry['action'] = MbAction::class;

        Annotations::getManager()->registry['shortcode'] = MbShortCode::class;

        Annotations::getManager()->registry['asset'] = MbAsset::class;
    }
}