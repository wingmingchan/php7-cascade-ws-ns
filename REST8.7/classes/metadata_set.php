<?php
require_once( 'auth_test.php' );

use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

//$mode = 'all';
$mode = 'display';
$mode = 'dump';
//$mode = 'get';
$mode = 'set';
$mode = 'copy';
$mode = 'raw';

try
{
    $id = "4624645d8b7ffe831131a667a82cb3b5";
    $ms = $cascade->getAsset( a\MetadataSet::TYPE, $id );
    
    switch( $mode )
    {
        case 'all':
        case 'display':
            $ms->display();
            
            if( $mode != 'all' )
                break;
                
        case 'dump':
            $ms->dump( true );
            
            if( $mode != 'all' )
                break;
                
        case 'get':
            echo "ID: " .            $ms->getId() . BR .
                 "Name: " .          $ms->getName() . BR .
                 "Path: " .          $ms->getPath() . BR .
                 "Property name: " . $ms->getPropertyName() . BR .
                 "Site name: " .     $ms->getSiteName() . BR .
                 "Type: " .          $ms->getType() . BR .
                 
                 "Author field required: " . 
                 u\StringUtility::boolToString( $ms->getAuthorFieldRequired() ) . BR .
                 "Author field visibility: " . 
                 $ms->getAuthorFieldVisibility() . BR .
                 "Description field required: " . 
                 u\StringUtility::boolToString( $ms->getDescriptionFieldRequired() ) . BR .
                 "Description field visibility: " . 
                 $ms->getDescriptionFieldVisibility() . BR .
                 "Display name field required: " . 
                 u\StringUtility::boolToString( $ms->getDisplayNameFieldRequired() ) . BR .
                 "Display name field visibility: " . 
                 $ms->getDisplayNameFieldVisibility() . BR .
                 "End date field required: " . 
                 u\StringUtility::boolToString( $ms->getEndDateFieldRequired() ) . BR .
                 "End date field visibility: " . 
                 $ms->getEndDateFieldVisibility() . BR .
                 
                 "Expiration folder field required: " . 
                 u\StringUtility::boolToString( $ms->getExpirationFolderFieldRequired() ) . BR .
                 "Expiration folder field visibility: " . 
                 $ms->getExpirationFolderFieldVisibility() . BR .
                 
                 "Keywords field required: " . 
                 u\StringUtility::boolToString( $ms->getKeywordsFieldRequired() ) . BR .
                 "Keywords field visibility: " . 
                 $ms->getKeywordsFieldVisibility() . BR .
                 "Review date field required: " . 
                 u\StringUtility::boolToString( $ms->getReviewDateFieldRequired() ) . BR .
                 "Review date field visibility: " . 
                 $ms->getReviewDateFieldVisibility() . BR .
                 "Start date field required: " . 
                 u\StringUtility::boolToString( $ms->getStartDateFieldRequired() ) . BR .
                 "Start date field visibility: " . 
                 $ms->getStartDateFieldVisibility() . BR .
                 "Summary field required: " . 
                 u\StringUtility::boolToString( $ms->getSummaryFieldRequired() ) . BR .
                 "Summary field visibility: " . 
                 $ms->getSummaryFieldVisibility() . BR .
                 "Teaser field required: " . 
                 u\StringUtility::boolToString( $ms->getTeaserFieldRequired() ) . BR .
                 "Teaser field visibility: " . 
                 $ms->getTeaserFieldVisibility() . BR .
                 "Title field required: " . 
                 u\StringUtility::boolToString( $ms->getTitleFieldRequired() ) . BR .
                 "Title field visibility: " . 
                 $ms->getTitleFieldVisibility() . BR ;
            
            if( $ms->hasDynamicMetadataFieldDefinitions() )
            {
                u\DebugUtility::dump( $ms->getDynamicMetadataFieldDefinitionNames() );
                u\DebugUtility::dump( $ms->getDynamicMetadataFieldDefinitionsStdClass() );
                u\DebugUtility::dump( $ms->getDynamicMetadataFieldPossibleValueStrings( "age" ) );
                u\DebugUtility::dump( $ms->getDynamicMetadataFieldPossibleValueStrings( "hobbies" ) );
            }
            
            u\DebugUtility::dump( $ms->getMetadata()->toStdClass() );
            
            $name = "show-intra-icon";
            
            if( $ms->hasDynamicMetadataFieldDefinition( $name ) )
            {
                echo "Definition found" . BR;
                $dmd = $ms->getDynamicMetadataFieldDefinition( $name );
                u\DebugUtility::dump( $dmd );
                u\DebugUtility::dump( $dmd->getPossibleValueStrings() );
            }
            
            $ms->dump();
            if( $mode != 'all' )
                break;
                
        case 'set':
/*/      
            $ms->
                setAuthorFieldRequired( false )->
                setAuthorFieldVisibility( a\MetadataSet::INLINE )->
                setDescriptionFieldRequired( false )->
                setDescriptionFieldVisibility( a\MetadataSet::INLINE )->
                setDisplayNameFieldRequired( false )->
                setDisplayNameFieldVisibility( a\MetadataSet::INLINE )->
                setEndDateFieldRequired( false )->
                setEndDateFieldVisibility( a\MetadataSet::INLINE )->
                setKeywordsFieldRequired( false )->
                setKeywordsFieldVisibility( a\MetadataSet::INLINE )->
                setReviewDateFieldRequired( false )->
                setReviewDateFieldVisibility( a\MetadataSet::INLINE )->
                setStartDateFieldRequired( false )->
                setStartDateFieldVisibility( a\MetadataSet::INLINE )->
                setSummaryFieldRequired( false )->
                setSummaryFieldVisibility( a\MetadataSet::INLINE )->
                setTeaserFieldRequired( false )->
                setTeaserFieldVisibility( a\MetadataSet::INLINE )->
                setTitleFieldRequired( false )->
                setTitleFieldVisibility( a\MetadataSet::INLINE );
        
            $name = "gender";
            
            if( $ms->hasDynamicMetadataFieldDefinition( $name ) )
            {
                echo S_PRE;
                var_dump( 
                    $ms->getDynamicMetadataFieldPossibleValueStrings( $name ) );
                echo E_PRE;
            
                $ms->setSelectedByDefault( $name, "Female" );
                $ms->unsetSelectedByDefault( $name, "Male" );
            }
            
            echo S_PRE;
            $ms->appendValue( $name, "Undetermined" )->
                setSelectedByDefault( $name, "No" )->
                setLabel( $name, "Undetermined" )->
                setRequired( $name, false )->
                setVisibility( $name, c\T::INLINE )->
                edit()->dump();
            echo E_PRE;
                
            $ms->//removeValue( $name, "Maybe" )->
                swapValues( $name, "Female", "Male" );
/*/               
            /*
            $field = 'text';
            
            if( $ms->hasDynamicMetadataFieldDefinition( $field ) )
            {
                // can be removed only once
                $ms->removeDynamicMetadataFieldDefinition( $field );
            }
            */
            
            $field1 = "age";
            $field2 = "hobbies";
            $field3 = "languages";
            
            if( $ms->hasDynamicMetadataFieldDefinition( $field1 ) &&  
                $ms->hasDynamicMetadataFieldDefinition( $field2 ) && 
                $ms->hasDynamicMetadataFieldDefinition( $field3 )
            )
            {
                $ms->swapDynamicMetadataFieldDefinitions( $field1, $field2 )->
                    swapDynamicMetadataFieldDefinitions( $field1, $field3 );
            }
                
            if( $mode != 'all' )
                break;
                
        case 'copy':
            $new_name = 'Test 2';
            $new_ms   = $ms->copy( 
                $cascade->getAsset( 
                    a\MetadataSetContainer::TYPE, $ms->getParentContainerId() ), 
                $new_name );
            $new_ms->dump( true );
            
            if( $mode != 'all' )
                break;
                
        case 'raw':
            $ms = $service->retrieve( 
                $service->createId( c\T::METADATASET, $id ), c\P::METADATASET );
            $ms->authorFieldRequired = false;
            $asset = new \stdClass();
            $asset->metadataSet = $ms;
            $service->edit( $asset );
            
            $ms = $service->retrieve( 
                $service->createId( c\T::METADATASET, $id ), c\P::METADATASET );

            u\DebugUtility::dump( $ms );
            
            if( $mode != 'all' )
                break;
    }
}
catch( \Exception $e )
{
    echo S_PRE . $e . E_PRE;
}
catch( \Error $er )
{
    echo S_PRE . $er . E_PRE;
}
?>