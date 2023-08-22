<span class="gm_omniprice" style="color:{$gm_omniprice_color}; background-color: {$gm_omniprice_background};">
    {l s='Lowest price within %d days before promotion:' sprintf=[$gm_omniprice_days] mod='gm_omniprice'}
    <span class="gm_omniprice_lowest" style="color:{$gm_omniprice_price_color};">{$gm_omniprice_lowest}</span>
    {if $gm_omniprice_show_real_discount && $gm_omniprice_real_discount}
        <span class="gm_omniprice_real_discount">({$gm_omniprice_real_discount})</span>
    {/if}    
</span>