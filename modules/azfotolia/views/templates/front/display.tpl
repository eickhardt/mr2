<div id="azfotolia">
    <h1>Fotolia</h1>
    
    <ul>
    {foreach from=$results key=k item=result}
        {if is_numeric($k)}
            <li>Matching media ID: {$result['id']}</li>
        {/if}
    {/foreach}
    </ul>
</div>
