<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AssetCompressor;

require_once __DIR__."/../minify/src/loadAll.php";

use MatthiasMullie\PathConverter\NoConverter;
use MatthiasMullie\PathConverter\Converter;

/**
 * Description of class
 *
 * @author TSassen
 */
class RelativePathFixer extends Converter{

    private $root;

    public function __construct($from, $to, $root = '') {
        parent::__construct($from, $to, $root);
        $this->from=$this->normalize(dirname($from));
        $this->root=$this->normalize(\PATH_ROOT);
        if(strpos($this->from, $this->root)!==false){
            $this->from= substr($this->from, strlen($this->root));
        }
    }
    
    public function convert($path): string {
        $url= \Gdn::request()->url("$this->from/$path",'//');
        return $url;
    }

}
