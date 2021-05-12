<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AssetCompressor;

use Gdn;

/**
 * Description of class
 *
 * @author TSassen
 */
class FileFinder {
    
    public function getFilePathFromTag($tag)
    {
        if(!is_array($tag)){
            return $tag;
        }
        $link = $tag['_tag']==='link'?$tag['href']:$tag['src'];
        $path_end= strrpos($link, '?');
        if($path_end!==false){
            $link= substr($link, 0,$path_end);
        }
        if(is_file($link)){
            return $link;
        }
        if(is_file(PATH_ROOT.$link)){
            return PATH_ROOT.$link;
        }
        if(filter_var($link,FILTER_VALIDATE_URL)){
            return $this->getPathFromURL($link);
        }
        return $link;
    }
    private function getPathFromURL($url){
        $webroot=Gdn::request()->webRoot();
        $rootstart= strpos($url, $webroot?$webroot:'//');
        if($rootstart === false){
            return $url;
        }
        $rootstart+=$webroot?strlen($webroot):2;
        $path_start=strpos($url, '/',$rootstart);
        $fullpath=PATH_ROOT.substr($url, $path_start);
        return is_file($fullpath)?$fullpath:false;
    }

}
