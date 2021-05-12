{foreach $cssfiles as $file}
    <link rel="stylesheet" href="{$file.href|url}">
{/foreach}
<style>
    {foreach $inlinecss as $line}
        {$line unfiltered}
    {/foreach}
</style>
{foreach $jsfiles as $file}
    <script src="{$file.src|url}"></script>
{/foreach}
<script>
    {foreach $inlinejs as $line}
        {$line unfiltered}
    {/foreach}
</script>
{foreach $deferred as $file}
    <script src="{$file.src|url}" defer="defer"></script>
{/foreach}
