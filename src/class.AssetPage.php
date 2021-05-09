<?php

namespace AssetCompressor;

class AssetPage {

    public function renderCompressedAsset($Args) {
        $content_type=$this->getContentType($Args);
        $headers = \apache_request_headers();
        if ($content_type ===false || strpos($headers['Accept-Encoding'], 'gzip') === false) {
            return;
        }
        $Args[] = 'gz';
        $fn = Compressor::path(...$Args);
        header('Content-Encoding: gzip');
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($fn))) {
            // Client's cache IS current, so we just respond '304 Not Modified'.
            \header('Last-Modified: ' . \gmdate('D, d M Y H:i:s', \filemtime($fn)) . ' GMT', true, 304);
            return;
        }
        \header('Last-Modified: ' . \gmdate('D, d M Y H:i:s', \filemtime($fn)) . ' GMT', true, 200);
        \header('Content-Length: ' . \filesize($fn));
        \header("Content-Type: $content_type;charset=UTF-8");
        \readfile($fn);
    }
    private function getContentType($Args){
        if(!(count($Args)===3 && ctype_xdigit($Args[0]) && is_numeric($Args[1]))){
            return false;
        }
        switch($Args[2]){
            case 'js':
                $content_type="text/javascript";
                break;
            case 'css':
                $content_type="text/css";
                break;
            default:
                return false;
        }
        return $content_type;
    }

}
