<?php

namespace AssetCompressor;

require_once __DIR__ . "/../minify/src/loadAll.php";

use MatthiasMullie\Minify\JS;
use MatthiasMullie\PathConverter\NoConverter;
/**
 * Modified minifier class
 *
 * For original project https://github.com/matthiasmullie/minify/issues
 *
 * Original @author Matthias Mullie <minify@mullie.eu>
 * @copyright Copyright (c) 2012, Matthias Mullie. All rights reserved
 * @license MIT License
 */
class JSMinifier extends JS {

    protected function stripWhitespace($content) {
        // uniform line endings, make them all line feed
        $content = str_replace(array("\r\n", "\r"), "\n", $content);

        // collapse all non-line feed whitespace into a single space
        $content = preg_replace('/[^\S\n]+/', ' ', $content);

        // strip leading & trailing whitespace
        $content = str_replace(array(" \n", "\n "), "\n", $content);

        // collapse consecutive line feeds into just 1
        $content = preg_replace('/\n+/', "\n", $content);

        // get rid of remaining whitespace af beginning/end
        return trim($content);
    }

}
