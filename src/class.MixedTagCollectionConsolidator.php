<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AssetCompressor;

/**
 * Description of class
 *
 * @author TSassen
 */
class MixedTagCollectionConsolidator {

    private $compressedTags;

    private function init() {
        $this->compressedTags = [];
        $this->notParsed = [];
        $this->scripts = [];
        $this->sheets = [];
        $this->jsSrc = [];
        $this->cssSrc = [];
        $this->deferred = [];
        $this->startPriority = 1;
        $this->filefinder=new FileFinder();
    }

    private function addCssTagToBuffer($tag) {
        $this->sheets[] = $tag;
        $this->cssSrc[] = $tag['href'];
    }

    private function addExternalJSTagToBuffer($tag) {
        if (isset($tag['defer'])) {
            $this->deferredName[]=$this->filefinder->getFilePathFromTag($tag);
            $this->deferred[]=$tag;
            return;
        }
        $this->scripts[] = $tag;
        $this->jsSrc[] = $tag['src'];
    }

    private function addOtherTagToBuffer($tag) {
        $this->flushToCompressedTags($this->scripts, $this->jsSrc, $this->startPriority, 'js');
        $this->startPriority = $tag['_sort'] + 1;
        $this->notParsed[] = $tag;
        $this->scripts = [];
        $this->jsSrc = [];
    }

    public function consolidateTags($tags) {
        $this->init();
        foreach ($tags as $tag) {
            if ($tag['_tag'] === 'link' && $tag['rel'] === 'stylesheet') {
                $this->addCssTagToBuffer($tag);
                continue;
            }
            if ($tag['_tag'] === 'script' && isset($tag['src'])) {
                $this->addExternalJSTagToBuffer($tag);
                continue;
            }
            $this->addOtherTagToBuffer($tag);
        }
        $this->flushToCompressedTags($this->sheets, $this->cssSrc, 0, 'css');
        $this->flushToCompressedTags($this->scripts, $this->jsSrc, $this->startPriority, 'js');
        AssetManager::getManager()->addDeferredScripts($this->deferred,$this->deferredName);
        return \ArrayConsolidator::mergeToArray($this->notParsed, ...$this->headerTags);
    }

    private $headerTags = [];

    private function flushToCompressedTags($tags, $names, $priority, $type) {
        if (empty($tags) || empty($names)) {
            return;
        }
        $compressor = new Compressor();
        $newtags = $compressor->getNecessaryHeaderTags($tags, $names, $priority, $type);
        $this->headerTags[] = $newtags;
    }

}
