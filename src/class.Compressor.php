<?php

namespace AssetCompressor;

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use MatthiasMullie\Minify\Minify;

class Compressor {

    private $minifier;
    private $leftoverTags;

    public function __construct() {
        
    }

    public function getMinifier() {
        if (!$this->minifier) {
            $this->minifier = new Minifier();
        }
        return $this->minifier;
    }

    public function getNecessaryHeaderTags($tags, $names, $priority, $type) {
        if (count($tags) < 1) {
            return [];
        }
        $sign = $priority > 0 ? 1 : -1;
        $entry = $this->getInfoEntry($tags, $names, $type);
        $unparsable = $entry->strip_redundant_tags($tags);
        $num = $entry->getNumberOfFiles();
        $hash = $entry->getHash();
        $newtags = [];
        for ($i = 0; $i < $num; $i++) {
            $src = \Gdn::request()->url("/plugin/AssetCompressor/$hash/$i/$type");
            if ($type === 'js') {
                $newtags[] = ["_tag" => 'script', "src" => $src, "_sort" => $priority + $sign * $i];
            } else {
                $newtags[] = ["_tag" => 'link', 'rel' => 'stylesheet', "href" => $src, "_sort" => $priority + $sign * $i];
            }
        }
        return \ArrayConsolidator::mergeToFixedArrayObject($newtags, $unparsable);
    }

    private function getInfoEntry($tags, $sources, $type) {
        $hash = $this->calchash($sources);
        [$needsUpdate, $info] = $this->needsUpdate($type, $hash);
        if ($needsUpdate) {
            $this->update($tags, $info);
        }
        return $info;
    }

    private function update($tags, InfoEntry $info) {
        $metaFile = self::path($info->getHash(), $info->getType(), 'dat');
        $metaDir = dirname($metaFile);
        if (!is_dir($metaDir)) {
            mkdir($metaDir);
        }
        $this->compressContent($tags, $info);
        $info->setUnparsableTags($this->leftoverTags);
        file_put_contents($metaFile, $info->serialize());
        return $this->leftoverTags;
    }

    private function compressContent($tags, $info) {
        $minifier = $info->getType() === 'js' ? new JSMinifier() : new CSSMinifier();
        $fileFinder = new FileFinder();
        $this->leftoverTags = [];
        foreach ($tags as $tag) {
            $path = $fileFinder->getFilePathFromTag($tag);
            if (($tag['_hint'] ?? false) !== 'inline' && $path) {
                $minifier->add($path);
                continue;
            }
            if (!empty($tag['src'])) {
                $this->leftoverTags[$tag['src']] = true;
            }
            if (!empty($tag['href'])) {
                $this->leftoverTags[$tag['href']] = true;
            }
        }
        $count = $info->countFile();
        $minifier->gzip(self::path($info->getHash(), $count, $info->getType(), 'gz'));
    }

    private function needsUpdate($type, $hash) {
        $info = new InfoEntry();
        $info->setHash($hash)->setType($type);
        $leftover = self::path($hash, $type . '.dat');
        if (file_exists($leftover)) {
            $info->initFromFile(file_get_contents($leftover));
            return [false, $info];
        }
        return [true, $info];
    }

    private function calcHash($sources) {
        sort($sources);
        return \md5(\implode('', $sources));
    }

    static function path(...$params) {
        return \Gdn::addonManager()->getCacheDir() . "/compressedAssets/" . \implode('.', $params);
    }

}
