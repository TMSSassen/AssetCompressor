<?php
namespace AssetCompressor;

if (!defined('APPLICATION')) {
    exit();
}
if (!function_exists('\apache_request_headers')) {
    require_once __DIR__.'/src/apache_request_headers_comp.php';
}

use Gdn_Plugin;
use HeadModule;

class AssetCompressorPlugin extends Gdn_Plugin {
    
    
    public function headmodule_BeforeToString_handler(HeadModule $Sender,$Args)
    {       
        $headers = \apache_request_headers();
        if (strpos($headers['Accept-Encoding'],'gzip')===false){
            return;
        }
        $consolidator=new MixedTagCollectionConsolidator();
        $allTags=$Sender->tags();
        $stillNecessary=$consolidator->consolidateTags($allTags);
        if($stillNecessary){
            $Sender->tags($stillNecessary);
        }
    }
    
    public function base_afterbody_handler($Sender){
        AssetManager::printLinks();
    }
    
    public function PluginController_AssetCompressor_create($Args){
        $page=new AssetPage();
        $page->renderCompressedAsset($Args);
    }

}