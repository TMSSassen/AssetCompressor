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
class UrlToPathConverter {
    
    public function getPath($tag)
    {
        if(!is_array($tag)){
            return $tag;
        }
        $link = $tag['_tag']==='link'?$tag['href']:$tag['src'];
        return $this->getPathFromURL($link);
    }
    private function getPathFromURL($url){
        $wroot=\Gdn::request()->webRoot();
        $rootstart= strpos($url, $wroot);
        if($rootstart === false){
            return $url;
        }
        $path_start=$this->getPathStart($url, $rootstart, $wroot);
        $path_end= strrpos($url, '?');
        $fullpath=PATH_ROOT;
        $fullpath.=$path_end === false ? substr($url, $path_start)
                :substr($url, $path_start,$path_end-$path_start);
        return is_file($fullpath)?$fullpath:false;
    }
    private function getPathStart($url,$rootstart,$wroot){
        $path_start1= strpos($url, '/',$rootstart+ strlen($wroot));
        $path_start2= strpos($url, '\\',$rootstart+ strlen($wroot));
        if($path_start1 === false){
            return $path_start2;
        }
        if($path_start2 === false){
            return $path_start1;
        }
        return $path_start1<$path_start2?$path_start1:$path_start2;
    }

}
