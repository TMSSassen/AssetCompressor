<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AssetCompressor;

use Smarty;

/**
 * Description of class
 *
 * @author TSassen
 */
class AssetManager {

    private $jsfiles = [], $js = [], $cssfiles = [], $css = [], $jsSrc = [], $cssSrc = [];
    private $smarty;

    public function addJSFile($js) {
        $this->jsfiles[] = ['_tag' => 'script', 'src' => $js];
        $this->jsSrc[] = $js;
    }

    public function addRawJS($js) {
        $this->js[] = $js;
    }

    public function addCSSFile($css) {
        $this->cssfiles[] = ['_tag' => 'link', 'href' => $css, 'rel' => 'stylesheet'];
        $this->cssSrc[] = $css;
    }

    public function addRawCSS($css) {
        $this->css[] = $css;
    }

    public function toString() {
        $headers = \apache_request_headers();
        $output = (strpos($headers['Accept-Encoding'], 'gzip') !== false) ? $this->getCompressed() : $this->getUncompressed();
        $this->jsfiles = $this->js = $this->cssfiles = $this->css = $this->jsSrc = $this->cssSrc = [];
        return $output;
    }

    public function getCompressed() {
        $compressor = new Compressor();
        $jsFileTags = $compressor->getNecessaryHeaderTags($this->jsfiles, $this->jsSrc,
                1, 'js');
        $cssFileTags = $compressor->getNecessaryHeaderTags($this->cssfiles, $this->cssSrc,
                0, 'css');
        $jsForInline = $compressor->getNecessaryHeaderTags($this->js, $this->js, 1, 'js');
        $cssForInline = $compressor->getNecessaryHeaderTags($this->css, $this->css, 0, 'css');
        $this->smarty->assign('tags', \ArrayConsolidator::consolidate($jsFileTags, $cssFileTags, $jsForInline, $cssForInline));
        return $this->smarty->fetch(__DIR__ . "/../template/compressed.tpl");
    }

    public function getUncompressed() {
        $this->smarty->assign('jsfiles', $this->jsfiles)->assign('inlinejs', $this->js)
                ->assign('cssfiles', $this->cssfiles)->assign('inlinecss', $this->css);
        return $this->smarty->fetch(__DIR__ . "/../template/uncompressed.tpl");
    }

    private function __construct() {
        $smarty = new Smarty();
        $smarty->registerPlugin('modifier', 'url', 'asset');
        $smarty->assign('escape_html', true);
        $this->smarty = $smarty;
    }

    static function getManager() {
        static $manager = null;
        if (!$manager) {
            $manager = new AssetManager();
        }
        return $manager;
    }

    static function addJS($js) {
        if (is_array($js)) {
            foreach ($js as $single) {
                self::addJS($single);
            }
            return;
        }
        if (is_file($js)) {
            self::getManager()->addJSFile($js);
            return;
        }
        self::getManager()->addRawJS($js);
    }

    static function addCSS($css) {
        if (is_array($css)) {
            foreach ($css as $single) {
                self::addCSS($single);
            }
            return;
        }
        if (is_file($css)) {
            self::getManager()->addCSSFile($css);
            return;
        }
        self::getManager()->addRawCSS($css);
    }

    static function printLinks() {
        echo self::getManager()->toString();
    }

}
