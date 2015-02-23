<?php
/*
	Copyright (C) 2012 Vernon Systems Limited

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
if ($css_class == "") {
	echo '<div class="ehive-search">';
} else {
	echo '<div class="ehive-search '.$css_class.'">';
}

if ($options['hide_search_form_enabled'] != 'on') {
	echo '<form class="ehive-search" name="ehive-search-form" action="'. $eHiveAccess->getSearchPageLink() .'" method="get">';
		
		echo "<input class='ehive-query' type='text' name='". $options['query_var'] ."' value='".$query."'/>";
		
		echo '<input class="ehive-submit" type="submit" value="Search"/>';
	echo '</form>';
}

if (!isset($eHiveApiErrorMessage)) {
	if (isset($objectRecordsCollection)) {	
		
		if ($objectRecordsCollection->totalObjects > 0) {
			echo '<div class="ehive-search-results">';				
				$view = ehive_get_var('view', $resultsViewDefault);		
				echo '<div class="ehive-navigation">';
					echo '<div class="ehive-pagination">';		
						if ( $a ) {
							$all = ($queryAll==true) ? '&all=true' : '';
							
							$pBase = ehive_link2('Search') . '?a='.$a.$all.'&view='.$view.'&%_%';
							$pFormat = $options['page_var'] . '=%#%';
							$pTotal = ceil($objectRecordsCollection->totalObjects / $options['limit']);
							$pCurrent = ehive_get_var($options['page_var'], 1);
							
							echo paginate_links( array('base' => $pBase, 'format' => $pFormat, 'total' => $pTotal, 'current' => $pCurrent) );
						
						} else {		
							$all = ($queryAll==true) ? '&all=true' : '';
									
							$pBase = ehive_link2('Search') . '?' . $options['query_var'] . '=' .rawurlencode(utf8_encode($query)).$all.'&view='.$view.'&%_%';
							
							$pFormat = $options['page_var'] . '=%#%';
							$pTotal = ceil($objectRecordsCollection->totalObjects / $options['limit']);
							$pCurrent = ehive_get_var($options['page_var'], 1);
							
							echo paginate_links( array('base' => $pBase, 'format' => $pFormat, 'total' => $pTotal, 'current' => $pCurrent) );
						} 	
						
					echo '</div>';				
					resultsViewNavigation($options, $resultsViewDefault, $resultsViewLightboxEnabled, $resultsViewListEnabled, $resultsViewPosterboardEnabled);				
				echo '</div>';
						
				$imageSize = "";
				$itemInlineStyleEnabled = false;
				$imageInlineStyleEnabled = false;
				$itemInlineStyle = '';
				$imageInlineStyle = '';
				$lightboxColumns = $options['lightbox_columns'];
				
				if ($view == 'lightbox') {							
					$imageSize = "s";
					
					if (isset($options['plugin_css_enabled']) && isset($lightboxColumns) && $lightboxColumns > 0) {
						$sectionWidth = 100.0/$lightboxColumns;
						$sectionMargin = $sectionWidth/($lightboxColumns*$lightboxColumns);
						$sectionWidth = $sectionWidth - $sectionMargin - 1.40;
					
						$itemInlineStyle .= "max-width: $sectionWidth%; width: $sectionWidth%; margin-right: $sectionMargin%; ";
						$itemInlineStyleEnabled = true;
					}
					
				} else {
					$imageSize = "ts";
				}
			
				if ($options['item_background_colour_enabled'] == 'on') {
					$itemInlineStyle .= "background:$item_background_colour; ";
					$itemInlineStyleEnabled = true;
				}
				if ($options['item_border_colour_enabled'] == 'on' && $item_border_width > 0) {
					$itemInlineStyle .= "border-style:solid; border-color:$item_border_colour; ";
					$itemInlineStyle .= "border-width:{$item_border_width}px; *margin:-{$item_border_width}px; ";
					$itemInlineStyleEnabled = true;
				}
				if ($options['image_background_colour_enabled'] == 'on') {
					$imageInlineStyle .= "background:$image_background_colour; ";
					$imageInlineStyleEnabled = true;
				}
				if ($options['image_padding_enabled'] == 'on') {
					$imageInlineStyle .= "padding:{$image_padding}px; ";
					$imageInlineStyleEnabled = true;
				}
				if ($options['image_border_colour_enabled'] == 'on' && $image_border_width > 0) {
					$imageInlineStyle .= "border-style:solid; border-color:$image_border_colour; ";
					$imageInlineStyle .= "border-width:{$image_border_width}px; ";
					$imageInlineStyleEnabled = true;
				}
				
				if($itemInlineStyleEnabled) {
					$itemInlineStyle = " style='$itemInlineStyle'";
				}
				if($imageInlineStyleEnabled) {
					$imageInlineStyle = " style='$imageInlineStyle'";
				}
				
				echo "<div class='ehive-view ehive-$view'>";		
					foreach ($objectRecordsCollection->objectRecords as $objectRecord) {
						echo "<div class='ehive-item' $itemInlineStyle>";
						
							echo '<div class="ehive-item-image-wrap">';
								echo '<a class="ehive-image-link" href="'.$eHiveAccess->getObjectDetailsPageLink($objectRecord->objectRecordId).'">';
								
									$imageLink = '<img class="ehive-image" src="'.EHIVE_SEARCH_PLUGIN_DIR.'images/no_image_ts.png" alt="'.$objectRecord->name.'" title="'.$objectRecord->name.'" >';
									
									$imageMediaSet = $objectRecord->getMediaSetByIdentifier('image');
									if (isset($imageMediaSet)) {
										$mediaRow = $imageMediaSet->mediaRows[0];
										$imageMedia = $mediaRow->getMediaByIdentifier("image_$imageSize");
										$imageLink = '<img class="ehive-image" src="'.$imageMedia->getMediaAttribute('url').'"' . $imageInlineStyle . ' alt="'.$imageMedia->getMediaAttribute('title').'" title="'.$imageMedia->getMediaAttribute('title').'">';
									}
									
									echo $imageLink;
								echo '</a>';
							echo '</div>';
						
							echo '<div class="ehive-item-metadata-wrap">';
								if ($view == 'lightbox') {
									lightboxMetadata( $options, $objectRecord, $eHiveAccess );
								} else {
									listMetadata( $options, $objectRecord, $eHiveAccess, $eHiveApi );
								}
							echo '</div>';						
						echo '</div>';
					}
				echo '</div>';
			
			if ( $poweredByEhiveEnabled ) {
				echo '<a href="http://ehive.com/what-is-ehive" target="_blank"><img class="ehive-logo-powered-by" src="'.EHIVE_SEARCH_PLUGIN_DIR.'images/powered_by_ehive_small.png" alt="Powered by eHive" title="Powered by eHive" width="103" height="35"></a>';				
			}
			echo '</div>';
								
		}  else {
			echo '<p class="ehive-no-results">There were no results found</p>';
		}
	} 
} else {
	echo "<p class='ehive-error-message ehive-account-details-error'>$eHiveApiErrorMessage</p>";	
}
echo '</div>';


function resultsViewNavigation($options, $resultsViewDefault, $resultsViewLightboxEnabled, $resultsViewListEnabled, $resultsViewPosterboardEnabled) {
	
	$view = ehive_get_var('view', $resultsViewDefault);	
	
	$queryAll = ehive_get_var('all', false);
	$all = ($queryAll==true) ? '&all=true' : '';
		
	$currentPage = '';
	$pageNumber = ehive_get_var($options['page_var']);
	if (isset($options['page_var']) && isset($pageNumber) && $pageNumber > 1) {
		$currentPage.="&{$options['page_var']}=$pageNumber";
	}
	
	$link = ehive_link2('Search').'?'. $options['query_var'].'='.urlencode(utf8_encode( ehive_get_var($options['query_var']))).$all.$currentPage;	

	if ($resultsViewLightboxEnabled && $resultsViewListEnabled) { 			
		
		echo '<ul class="ehive-view-navigation">';		
		echo '<li>View by:</li>';
				
		if ($resultsViewLightboxEnabled) {
			if ($view == 'lightbox') {
				echo '<li>lightbox</li>';
			} else {
				echo '<li><a href="'.$link.'&view=lightbox'.'">lightbox</a></li>';
			}
		}			
		
		if ($resultsViewListEnabled) {
			if ($view == 'list') {
				echo '<li>list</li>';
			} else {
				echo '<li><a href="'.$link.'&view=list'.'">list</a></li>';
			}
		}
						
		echo '</ul>';
	}
}

function lightboxMetadata($options, $objectRecord, $eHiveAccess) {
	$items = array(	'object_number' => 'object_number',
					'name' => 'name',
					'primary_creator_maker' => 'primary_creator_maker',
					'primary_creator_maker_role' => 'primary_creator_maker',
					'taxonomic_classification' => 'taxon',
					'taxonomic_type_indicator' => 'taxon',
					'field_collector' => 'field_collector',
					'web_public_description' => 'web_public_description',
					'date_made' => 'date_made',
					'place_made' => 'place_made',
					'object_type' => 'object_type',
					'medium_description' => 'medium_description',
					'measurement_description' => 'measurement_description',
					'named_collection' => 'named_collection',
					'credit_line' => 'credit_line'
	);
		
	$enabledFields = array();
	
	foreach($items as $key => $value) {
		$fieldLabel = null;
		$fieldValue = null;
		
		
		if ($options["lightbox_{$key}_enabled"] == 'on') {
			$fieldSet = $objectRecord->getFieldSetByIdentifier($value);
			if (isset($fieldSet)) {
				$fieldRow = $fieldSet->fieldRows[0];
				$field = $fieldRow->getFieldByIdentifier($key);
				
				if (isset($field) && $field != null) {
					$fieldValue = $field->getFieldAttribute('value');
				} else {
					continue;
				}
			}
			$fieldLabel = $options["lightbox_{$key}_enabled_label"];
		}
		if (  isset($fieldValue) && $fieldValue !='' ) {		
			echo '<p class="ehive-field ehive-identifier-'.$key.'">';
			if (isset($fieldLabel) && $fieldLabel !='') {
				echo '<span class="ehive-field-label">'.$fieldLabel.'</span>';
			}
			if (isset($fieldValue) && $fieldValue !='') {
				echo $fieldValue;
			}
			echo '</p>';
		}	
	}
	if ($options['lightbox_more_link_enabled'] == "on") {
		echo '<a class="ehive-more-link" href="'.$eHiveAccess->getObjectDetailsPageLink($objectRecord->objectRecordId).'">'.$options['lightbox_more_link_text'].'</a>';
	}
}

function listMetadata($options, $objectRecord, $eHiveAccess, $eHiveApi) {
	$showPublicProfileName = $options['show_public_profile_name'] == 'on' ? true : false;
	$showCatalogueTypeIcon = $options['show_catalogue_type_icon'] == 'on' ? true : false;
	
	echo '<a href="'.$eHiveAccess->getObjectDetailsPageLink($objectRecord->objectRecordId).'"><span class="ehive-item-summary">'.listFields($options, $objectRecord).'</span></a>';
		
	if ( $showPublicProfileName ) {
		echo '<p class="ehive-field ehive-identifier-public_profile_name">';
		echo '<span class="ehive-field-label">From: </span>';
		echo '<a href="'.$eHiveAccess->getAccountDetailsPageLink($objectRecord->account->accountId).'">'.$objectRecord->account->publicProfileName.'</a>';
		echo '</p>';
	}

	if ( $showCatalogueTypeIcon ) {
		switch ($objectRecord->catalogueType) {
			case 'archaeology':
				echo '<img class="ehive-catalogue-type ehive-archaeology" src="/wp-content/plugins/ehive-search/images/catalogue_archaeology.png" width="21" height="21" alt="Archaeology" title="Archaeology"/>';
				break;
			case 'archives':
				echo '<img class="ehive-catalogue-type ehive-archives" src="/wp-content/plugins/ehive-search/images/catalogue_archives.png" width="21" height="21" alt="Archives" title="Archives"/>';
				break;
			case 'art':
				echo '<img class="ehive-catalogue-type ehive-art" src="/wp-content/plugins/ehive-search/images/catalogue_art.png" width="21" height="21" alt="Art" title="Art"/>';
				break;
			case 'history':
				echo '<img class="ehive-catalogue-type ehive-history" src="/wp-content/plugins/ehive-search/images/catalogue_history.png" width="21" height="21" alt="History" title="History"/>';
				break;
			case 'library':
				echo '<img class="ehive-catalogue-type ehive-library" src="/wp-content/plugins/ehive-search/images/catalogue_library.png" width="21" height="21" alt="Library" title="Library"/>';
				break;
			case 'natural_science':
				echo '<img class="ehive-catalogue-type ehive-natural-science" src="/wp-content/plugins/ehive-search/images/catalogue_natural_science.png" width="21" height="21" alt="Natural Science" title="Natural Science"/>';
				break;
			case 'photography':
				echo '<img class="ehive-catalogue-type ehive-photography" src="/wp-content/plugins/ehive-search/images/catalogue_photography.png" width="21" height="21" alt="Photography" title="Photography"/>';
				break;
		}
	}

}

function listFields($options, $objectRecord) {
		
	$items = array(	'object_number' => 'object_number',
					'name' => 'name',
					'primary_creator_maker' => 'primary_creator_maker',
					'primary_creator_maker_role' => 'primary_creator_maker',
					'taxonomic_classification' => 'taxon',
					'taxonomic_type_indicator' => 'taxon',
					'field_collector' => 'field_collector',
					'web_public_description' => 'web_public_description',
					'date_made' => 'date_made',
					'place_made' => 'place_made',
					'object_type' => 'object_type',
					'medium_description' => 'medium_description',
					'measurement_description' => 'measurement_description',
					'named_collection' => 'named_collection',
					'credit_line' => 'credit_line'
	);
	
	$summary = '';
	
	foreach($items as $key => $value) {
		$fieldLabel = null;
		$fieldValue = null;
	
		if ($options["list_{$key}_enabled"] == 'on') {
			$fieldSet = $objectRecord->getFieldSetByIdentifier($value);
			if (isset($fieldSet)) {
				$fieldRow = $fieldSet->fieldRows[0];
				$field = $fieldRow->getFieldByIdentifier($key);
				
				if (isset($field) && $field != null) { 
					$fieldValue = $field->getFieldAttribute('value');
				} else {
					continue;
				}
			}
			if ($fieldValue != '') {
				if ($summary != '') {
					$summary = $summary.'; '.$fieldValue;
				} else {
					$summary = $fieldValue;
				}
			} 
		}		
	}
	return $summary;
}
?>