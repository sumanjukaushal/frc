<div n:class="'address-container', $meta->displaySocialIcons && count($meta->socialIcons) > 0 ? social-icons-displayed">
    <?php #echo "<pre>";print_r($meta);echo "</pre>";?>
	<h2>{__ 'FRC Fields'}</h2>
	<div class="content">
        {if $meta->wlm_city && !is_array($meta->wlm_city)}
		<div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Suburb'}:</h5></div>
			<div class="address-data"><p><span itemprop="telephone">{$meta->wlm_city}</span></p></div>
		</div>
        {/if}
        
        {if $meta->wlm_state && !is_array($meta->wlm_state)}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'State'}:</h5></div>
			<div class="address-data"><p><span itemprop="telephone">{$meta->wlm_state}</span></p></div>
		</div>
        {/if}
        
        {if $meta->wlm_zip && !is_array($meta->wlm_zip)}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Pin Code'}:</h5></div>
			<div class="address-data"><p><span itemprop="telephone">{$meta->wlm_zip}</span></div>
		</div>
        {/if}
		
        {if $meta->location && !is_array($meta->location)}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Location'}:</h5></div>
			<div class="address-data"><p><span itemprop="location">{$meta->location}</span></p></div>
		</div>
        {/if}
        
        {if $meta->access && !is_array($meta->access)}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Access'}:</h5></div>
			<div class="address-data"><p><span itemprop="access">{$meta->access}</span></p></div>
		</div>
        {/if}
        
        {if !empty($meta->responsible_authority)}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Responsible Authority'}:</h5></div>
			<div class="address-data"><p><span itemprop="telephone">{$meta->responsible_authority}</span></p></div>
		</div>
        {/if}
        
        {if $meta->discount && $meta->discount_detail}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Discount Detail'}:</h5></div>
			<div class="address-data"><p><span itemprop="discount_detail">{$meta->discount_detail}</span></p></div>
		</div>
        {/if}
        
        {if $meta->offer && $meta->offer_detail}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Offer Detail'}:</h5></div>
			<div class="address-data"><p><span itemprop="offer_detail">{$meta->offer_detail}</span></p></div>
		</div>
        {/if}
        
        {if $meta->seasonalRatesApply && $meta->seasonalRatesApply_detail}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Seasonal Rate Apply'}:</h5></div>
			<div class="address-data"><p><span itemprop="seasonal_rates_apply">{$meta->seasonalRatesApply_detail}</span></p></div>
		</div>
        {/if}
        
        {if $meta->limit_discount && $meta->per_month_uses}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Per Month Uses:'}:</h5></div>
			<div class="address-data"><p><span itemprop="per_month_uses" style='color:#f00;'>This Discount/Offer may only be used  {$meta->per_month_uses}  times in a one month period.</span></p></div>
		</div>
        {/if}
        
        {if $meta->pricing_detail}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Pricing Detail'}:</h5></div>
			<div class="address-data"><p><span itemprop="pricing_detail">{$meta->pricing_detail}</span></p></div>
		</div>
        {/if}
        
        {if $meta->booking_link}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>&nbsp;&nbsp;&nbsp;&nbsp;</h5></div>
			<div class="address-data"><p><span itemprop="booking_link"><a href='{$meta->booking_link}' target='_blank' style='color:red'>Click Here To Book</a></span></p></div>
		</div>
        {/if}
        
        {if $meta->police_check_required}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Police Check'}:</h5></div>
			<div class="address-data"><p><span itemprop="police_check">Yes</span></p></div>
		</div>
        {/if}
        
        {if $meta->pet_minding_required && $meta->pet_minding_addnl}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Pet Minding'}:</h5></div>
			<div class="address-data"><p><span itemprop="pet_minding">{$meta->pet_minding_addnl}</span></p></div>
		</div>
        {/if}
        
        {if $meta->gardening}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Gardening'}:</h5></div>
			<div class="address-data"><p><span itemprop="gardening">{$meta->gardening}</span></p></div>
		</div>
        {/if}
        
        {if $meta->other}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Other'}:</h5></div>
			<div class="address-data"><p><span itemprop="telephone">{$meta->other}</span></p></div>
		</div>
        {/if}
        
        {if $meta->start_date}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Start Date'}:</h5></div>
			<div class="address-data"><p><span itemprop="start_date">{$meta->start_date|date:"j M Y"}</span></p></div>
		</div>
        {/if}        
        
        {if $meta->end_date}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'End Date'}:</h5></div>
			<div class="address-data"><p><span itemprop="end_date">{$meta->end_date|date:"j M Y"}</span></p></div>
		</div>
        {/if}
        
        {if $meta->duration}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Duration'}:</h5></div>
			<div class="address-data"><p><span itemprop="duration">{$meta->duration}</span></p></div>
		</div>
        {/if}
        
        {if $meta->property_type}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Property Type'}:</h5></div>
			<div class="address-data"><p><span itemprop="property_type">{$meta->property_type}</span></p></div>
		</div>
        {/if}
        
        {if $meta->help_required}
        <div class="address-row row-telephone">
			<div class="address-name"><h5>{__ 'Help Required'}:</h5></div>
			<div class="address-data"><p><span itemprop="help_required">{$meta->help_required}</span></p></div>
		</div>
        {/if}
	</div>
</div>