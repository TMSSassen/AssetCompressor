# AssetCompressor
### *A Vanilla Forum plugin to combine and compress multiple CSS & JS files*

## When (not) to use AssetCompressor

Any time a file is requested from your server some overhead is incurred. 
Combining small files reduces the time spend on dealing with http requests. 
Additionally, Gzipping files can also save bandwidth, as virtually all modern browsers can unzip files client-side.

However, this is offset by time taken to combine and compress the necessary files.
This plugin aims to save server resources by caching files after combination, minimization and compression.

**_Important note: this means that for a regular Vanilla installation about 3-5 MB of disk space will be necessary for cache.
If your theme uses large Javascript libraries, this may be more. Also, minimization is not guaranteed error-free.
It is highly recommended to monitor the {root}/cache/compressedAssets folder to check whether this plugin isn't using excessive memory.
This plugin should be tested on a backed up copy of your forum before being applied to a live forum!_**

## Usage

This plugin uses the excellent [minifier library](https://github.com/matthiasmullie/minify/) developed by Matthias Mullie under the MIT License.
It analyzes all tags in the Vanilla Forums Head module of the controller, then depending on the kind of tag:
- CSS files are combined, minimized and compressed, then moved towards the top of the page.
- Javascript files are combined, minized and compressed, however their position relative to non-css tags remains the same.
- All other tags are kept as-is. Every time such a tag is encountered, the Javascript combination stops and starts anew.

This plugin also provides a way for other plugins to easily add assets.
It is recommended to check whether this plugin is enabled, then use the AssetManager class to add JS/CSS files.
These will be combined, minimized and compressed just before page end. 
The following snippet can be used in your plugin to either use the compressor when available, or inline code if not.

```php
private function addJS($js){
    if (Gdn::addonManager()->isEnabled('AssetCompressor', \Vanilla\Addon::TYPE_ADDON)) {
        AssetManager::addJS($js);
        return;
    }
    echo "<script>{$js}</script>";
}
private function addCss($css)
{
    if (Gdn::addonManager()->isEnabled('AssetCompressor', \Vanilla\Addon::TYPE_ADDON)) {
        AssetManager::addCss($css);
        return;
    }
    echo "<style>{$css}</style>";
}
 ```

## Technical details

On older browsers that do not support Gzip, the compressor is automatically disabled and regular files will be served.
Minimizing and compressing takes CPU time, however this is (in general) an operation which only has to happen once.
Compressed files are cached client side as well, using Last-Modified Headers.
If you change CSS or JS files, clear **both** the server cache directory and browser cache (ctrl+F5 in Chrome)
The source paths of requested assets are concatenated, then hashed with MD5, then compared to the server cache.
If the hash matches, the cached resource is served. If no similar hash can be found, a new file is created.

File order of similar typed-assets doesn't matter for the hashing - requesting file A,B and C will produce the same hash as requesting file C, A and B.

## Vanilla features used

This plugin uses the following features:
1. **PluginController_AssetCompressor_create:**
Is triggered when a client requests the '{your vanilla root}/plugin/AssetCompressor/{hash}/{file_id}/{file_type}' page.
Used to check if the browser of the client accepts gzipped assets, and if yes to lookup the correct file and serve it to the client.
As the cache folder is protected, the client cannot directly access the assets but has to be routed through this event.

2. **Base_afterbody_handler:**
Is triggered after the main content on every page. Used to add compressed JS and CSS assets of plugins to the page.
In this way, plugins can choose depending on (for instance) whether or not a post is displayed with a certain keyword to add or leave out stylesheets/scripts.

3. **HeadModule_beforeToString_handler**
Is trigged just before the Head module is rendered on every page.
Enables this plugin to intercept, compress and replace JS and CSS assets if possible.

## Reuse this template

If you want to reuse this template for your own plugin, feel free to.
Simply copy the contents of the 'docs' folder to a docs directory in your own plugin, and prepare a README.MD (possibly case sensitive) file in Markdown format in the same directory as your addon.json file.
Lastly, add "documentationUrl": "/plugins/AssetCompressor/docs/doc.html" to your addon.json file to make it accessible from the plugin dashboard page.

This will lead a folder structure like so:

```
-> [Your plugin folder]
| -> [addon.json]
| -> [class.YourPlugin.plugin.php]
| -> [README.MD]
| -> [docs folder]
| | -> [doc.html]
| | -> [doc.css]
| | -> [doc.js]
| | -> [marked.min.js]
```

Any H2 (##) headers are automatically used for navigation purposes as well.

## Attributions

- Icon - https://pixabay.com/vectors/sign-warning-hand-injury-crush-5673717/
- Minify and Pathconverter library - Matthias Mullie - https://github.com/matthiasmullie/
- Marked JS library - Christopher Jeffrey - https://github.com/markedjs/marked
- Multibutton CSS Ryan Mulligan https://codepen.io/hexagoncircle/pen/wvBmvOG
- Vanilla Forums open access (duh)