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

    public function consolidateTags(&$tags) {
        $this->compressedTags=[];
        $notParsed=[];
        $scripts  = $sheets = $jsSrc = $cssSrc = [];
        $startPriority=1;
        foreach ($tags as $tag) {
            if ($tag['_tag'] === 'link' && $tag['rel'] === 'stylesheet') {
                $sheets[] = $tag;
                $cssSrc[] = $tag['href'];
                continue;
            }
            if ($tag['_tag'] === 'script' && isset($tag['src'])) {
                $scripts[] = $tag;
                $jsSrc[] = $tag['src'];
                continue;
            }
            $this->flushToCompressedTags($scripts,$jsSrc,$startPriority,'js');
            $startPriority=$tag['_sort']+1;
            $notParsed[] = $tag;
            $scripts=[];
            $jsSrc=[];
        }
        $this->flushToCompressedTags($sheets,$cssSrc,0,'css');
        return array_merge($notParsed,$this->getCompressedTags());
    }
    private $headerTags=[];
    
    private function flushToCompressedTags($tags,$names,$priority,$type){
        if(empty($tags)||empty($names)){
            return;
        }
        $compressor=new Compressor();
        $newtags=$compressor->getNecessaryHeaderTags($tags, $names, $priority, $type);
        $this->headerTags= array_merge($this->headerTags,$newtags);
    }
    private function getCompressedTags(){
        return $this->headerTags;
    }
}
