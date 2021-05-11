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
class InfoEntry {

    private $hash;
    private $type;
    private $tags;
    private $numFiles=0;
    
    public function setType($type): InfoEntry
    {
        $this->type=$type;
        return $this;
    }
    public function setHash($hash): InfoEntry
    {
        $this->hash=$hash;
        return $this;
    }
    public function setUnparsableTags($tags): InfoEntry
    {
        $this->tags= \is_array($tags)? $tags : \json_decode($tags,true);
        if(!$this->tags){
            $this->tags=[];
        }
        return $this;
    }
    public function getHash(){
        return $this->hash;
    }
    public function getType()
    {
        return $this->type;
    }
    public function serialize(){
        $serialized=[];
        if($this->hash){
            $serialized['Hash']=$this->hash;
        }
        if($this->type){
            $serialized['Type']=$this->type;
        }
        if($this->numFiles){
            $serialized['Num']=$this->numFiles;
        }
        if($this->tags){
            $serialized['Unparsable_tags']= $this->tags;
        }
        return json_encode($serialized);
    }
    public function strip_redundant_tags($tags)
    {
        if(!$this->tags){
            $this->tags=[];
            return [];
        }
        $stillImportant=[];
        foreach($tags as $tag){
            $link = $tag['_tag']==='link'?$tag['href']:$tag['src'];
            if(isset($this->tags[$link])){
                $stillImportant[]=$tag;
            }
        }
        return $stillImportant;
    }
    public function initFromFile($file_contents){
        $arr= json_decode($file_contents,true);
        if(isset($arr['Hash'])){
            $this->hash=$arr['Hash'];
        }
        if(isset($arr['Type'])){
            $this->type=$arr['Type'];
        }
        if(isset($arr['Num'])){
            $this->numFiles=$arr['Num'];
        }
        if(isset($arr['Unparsable_tags'])){
            $this->tags=$arr['Unparsable_tags'];
        }
    }
    function countFile(){
        return $this->numFiles++;
    }
    function getNumberOfFiles(){
        return $this->numFiles;
    }
}
