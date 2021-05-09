{foreach $tags as $tag}
    {if $tag._tag=='link'}
        <link rel="stylesheet" href="{$tag.href}">
    {else}
        <script src="{$tag.src}"></script>
    {/if}
{/foreach}
