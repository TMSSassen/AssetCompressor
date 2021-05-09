<?php
/**
 * Modified minifier class
 *
 * For original project https://github.com/matthiasmullie/minify/issues
 *
 * Original @author Matthias Mullie <minify@mullie.eu>
 * @copyright Copyright (c) 2012, Matthias Mullie. All rights reserved
 * @license MIT License
 */
namespace AssetCompressor;

require_once __DIR__."/../minify/src/loadAll.php";

use MatthiasMullie\Minify\CSS;

class CSSMinifier extends CSS{
    
    protected function getPathConverter($source, $target) {
        return new RelativePathFixer($source, $target);
    }
}
