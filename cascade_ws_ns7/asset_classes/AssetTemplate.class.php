<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2018 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 5/22/2018 Added getEditorConfiguration.
  * 1/10/2017 Modified various objects for REST.
  * 9/28/2017 Added getCloudTransport.
  * 1/5/2017 Added getSearchInformation.
  * 8/26/2016 Added constant NAME_SPACE.
  * 3/15/2016 Fixed a bug in getFeedBlock.
  * 9/14/2015 Added getMetadata.
  * 5/28/2015 Added namespaces.
 */
namespace cascade_ws_asset;

use cascade_ws_constants as c;
use cascade_ws_AOHS as aohs;
use cascade_ws_utility as u;
use cascade_ws_exception as e;
use cascade_ws_property as p;

/**
<documentation>
<description><h2>Introduction</h2>

</description>
<postscript><h2>Test Code</h2><ul><li><a href=""></a></li></ul></postscript>
</documentation>
*/
class AssetTemplate
{
    const NAME_SPACE = "cascade_ws_asset";

    public static function getAssetFactory() : \stdClass
    {
        $af                      = new \stdClass ();
        $af->name                = "";
        $af->siteName            = "";
        $af->parentContainerPath = "";
        $af->assetType           = "";
        $af->workflowMode        = "";

        $asset               = new \stdClass ();
        $asset->assetFactory = $af;
        return $asset;
    }
    
    public static function getCloudTransport() : \stdClass
    {
        $transport             = new \stdClass ();
        $transport->name                = "";
        $transport->siteName            = "";
        $transport->parentContainerPath = "";
        $transport->key                 = "";
        $transport->secret              = "";
        $transport->bucketName          = "";
        $transport->basePath            = NULL;

        $asset                 = new \stdClass ();
        $asset->cloudTransport = $transport;
        return $asset;
    }

    public static function getContentType() : \stdClass
    {
        $ct                           = new \stdClass ();
        $ct->name                     = "";
        $ct->siteName                 = "";
        $ct->parentContainerPath      = "";
        $ct->pageConfigurationSetPath = "";
        $ct->metadataSetPath          = "";
        $ct->dataDefinitionPath       = NULL;

        $asset              = new \stdClass ();
        $asset->contentType = $ct;
        return $asset;
    }

    public static function getContainer( $property ) : \stdClass
    {
        $c                      = new \stdClass ();
        $c->name                = "";
        $c->siteName            = "";
        $c->parentContainerPath = "";

        $asset            = new \stdClass ();
        $asset->$property = $c;
        return $asset;
    }

    public static function getDatabaseTransport() : \stdClass
    {
        $transport                      = new \stdClass ();
        $transport->name                = "";
        $transport->siteName            = "";
        $transport->parentContainerPath = "";
        $transport->username            = "";
        $transport->serverName          = "";
        $transport->serverPort          = "";
        $transport->databaseName        = "";
        $transport->transportSiteId     = "";

        $asset                    = new \stdClass ();
        $asset->databaseTransport = $transport;
        return $asset;
    }

    public static function getDataDefinition() : \stdClass
    {
        $dd                      = new \stdClass ();
        $dd->name                = "";
        $dd->siteName            = "";
        $dd->parentContainerPath = "";
        $dd->xml                 = "";

        $asset                 = new \stdClass ();
        $asset->dataDefinition = $dd;
        return $asset;
    }
    
    public static function getDataDefinitionBlock() : \stdClass
    {
        $ddb                      = new \stdClass ();
        $ddb->name                = "";
        $ddb->siteName            = "";
        $ddb->parentContainerPath = "";
        $ddb->structuredData      = NULL;
        $ddb->xhtml               = NULL;

        $asset                           = new \stdClass ();
        $asset->xhtmlDataDefinitionBlock = $ddb;
        return $asset;
    }
    
    public static function getDataDefinitionPage() : \stdClass
    {
        $page                        = new \stdClass ();
        $page->name                  = "";
        $page->siteName              = "";
        $page->parentFolderPath      = "";
        $page->xhtml                 = NULL;
        $page->contentTypeId         = "";

        $asset       = new \stdClass ();
        $asset->page = $page;
        return $asset;
    }
    public static function getDestination() : \stdClass
    {
        $destination                      = new \stdClass ();
        $destination->name                = "";
        $destination->parentContainerPath = "";
        $destination->transportPath       = "";
        $destination->siteName            = "";

        $asset              = new \stdClass ();
        $asset->destination = $destination;
        return $asset;
    }
    
    public static function getDynamicMetadataFieldDefinition() : \stdClass
    {
        $dmfd                 = new \stdClass ();
        $dmfd->name           = "";
        $dmfd->label          = "";
        $dmfd->fieldType      = "";
        $dmfd->required       = false;
        $dmfd->visibility     = c\T::VISIBLE;
        $dmfd->possibleValues = NULL;

        $asset                                 = new \stdClass ();
        $asset->dynamicMetadataFieldDefinition = $dmfd;
        return $asset;
    }
    
    public static function getEditorConfiguration() : \stdClass
    {
        $ec                  = new \stdClass ();
        $ec->name            = "";
        $ec->id              = "";
        $ec->siteId          = "";
        $ec->siteName        = "";
        $ec->cssFileRecycled = false;
        $ec->configuration   = new \stdClass ();

        $asset                      = new \stdClass ();
        $asset->editorConfiguration = $ec;
        return $asset;
    }
    
    public static function getFacebookConnector() : \stdClass
    {
        $connector                   = new \stdClass ();
        $connector->name             = "";
        $connector->siteName         = "";
        $connector->parentFolderPath = "";
        $connector->destinationId    = "";

        $asset                    = new \stdClass ();
        $asset->facebookConnector = $connector;
        return $asset;
    }
    
    public static function getFeedBlock() : \stdClass
    {
        $feed_block                   = new \stdClass ();
        $feed_block->name             = "";
        $feed_block->siteName         = "";
        $feed_block->parentFolderPath = "";
        $feed_block->feedURL          = "";

        $asset            = new \stdClass ();
        $asset->feedBlock = $feed_block;
        return $asset;
    }
    
    public static function getFile() : \stdClass
    {
        $file                   = new \stdClass ();
        $file->name             = "";
        $file->siteName         = "";
        $file->parentFolderPath = "";

        $asset       = new \stdClass ();
        $asset->file = $file;
        return $asset;
    }
    
    public static function getFileSystemTransport() : \stdClass
    {
        $transport                      = new \stdClass ();
        $transport->name                = "";
        $transport->siteName            = "";
        $transport->parentContainerPath = "";
        $transport->directory           = "";

        $asset                      = new \stdClass ();
        $asset->fileSystemTransport = $transport;
        return $asset;
    }
    
    public static function getFolder() : \stdClass
    {
        $folder                   = new \stdClass ();
        $folder->name             = "";
        $folder->siteName         = "";
        $folder->parentFolderPath = "";
        $folder->includeInStaleContent = false;

        $asset         = new \stdClass ();
        $asset->folder = $folder;
        return $asset;
    }
    
    public static function getFormat( $property ) : \stdClass
    {
        $f                   = new \stdClass ();
        $f->name             = "";
        $f->siteName         = "";
        $f->parentFolderPath = "";
        
        if( $property == c\P::SCRIPTFORMAT )
        {
            $f->script = "";
            $f->xml    = NULL;
        }
        else
        {
            $f->script = NULL;
            $f->xml    = "";
        }

        $asset            = new \stdClass ();
        $asset->$property = $f;
        return $asset;
    }
    
    public static function getFtpTransport() : \stdClass
    {
        $transport                      = new \stdClass ();
        $transport->name                = "";
        $transport->siteName            = "";
        $transport->parentContainerPath = "";
        $transport->username            = "";
        $transport->password            = "";
        $transport->hostName            = "";
        $transport->port                = "";

        $asset               = new \stdClass ();
        $asset->ftpTransport = $transport;
        return $asset;
    }
    
    public static function getGoogleAnalyticsConnector() : \stdClass
    {
        $connector                   = new \stdClass ();
        $connector->name             = "";
        $connector->siteName         = "";
        $connector->parentFolderPath = "";

        $asset                           = new \stdClass ();
        $asset->googleAnalyticsConnector = $connector;
        return $asset;
    }
    
    public static function getGroup() : \stdClass
    {
        $group            = new \stdClass ();
        $group->groupName = "";
        $group->role      = 'Default';
        $group->users     = "";

        $asset        = new \stdClass ();
        $asset->group = $group;
        return $asset;
    }
    
    public static function getIndexBlock( $type ) : \stdClass
    {
        $block                       = new \stdClass ();
        $block->name                 = "";
        $block->parentFolderPath     = "";
        $block->siteName             = "";
        $block->indexBlockType       = $type;
        $block->maxRenderedAssets    = 0;
        $block->depthOfIndex         = 0;

        $asset             = new \stdClass ();
        $asset->indexBlock = $block;
        return $asset;
    }
    
    public static function getMetadata() : \stdClass
    {
        $m                  = new \stdClass ();
        $m->author          = NULL;
        $m->displayName     = NULL;
        $m->endDate         = NULL;
        $m->keywords        = NULL;
        $m->metaDescription = NULL;
        $m->reviewDate      = NULL;
        $m->startDate       = NULL;
        $m->summary         = NULL;
        $m->teaser          = NULL;
        $m->title           = NULL;
        $m->dynamicFields   = NULL;
        return $m;
    }
    
    public static function getMetadataSet() : \stdClass
    {
        $metadata_set                      = new \stdClass ();
        $metadata_set->name                = '';
        $metadata_set->parentContainerPath = '';
        
        $asset              = new \stdClass ();
        $asset->metadataSet = $metadata_set;
        return $asset;
    }
    
    public static function getPageConfiguration() : \stdClass
    {
        $pc                        = new \stdClass ();
        $pc->name                  = "";
        $pc->defaultConfiguration  = false;
        $pc->templateId            = NULL;
        $pc->templatePath          = NULL;
        $pc->formatId              = NULL;
        $pc->formatPath            = NULL;
        $pc->formatRecycled        = NULL;
        $pc->pageRegions           = new \stdClass ();
        $pc->outputExtension       = NULL;
        $pc->serializationType     = NULL;
        $pc->includeXMLDeclaration = false;
        $pc->publishable           = false;
        return $pc;
    }
    
    public static function getPageConfigurationSet() : \stdClass
    {
        $pcs                        = new \stdClass ();
        $pcs->name                  = "";
        $pcs->parentContainerId     = NULL;
        $pcs->parentContainerPath   = NULL;
        $pcs->pageConfigurations    = new \stdClass ();
        
        $asset                       = new \stdClass ();
        $asset->pageConfigurationSet = $pcs;
        return $asset;
    }
    
    public static function getPublishSet() : \stdClass
    {
        $publish_set                    = new \stdClass ();
        $publish_set->name              = "";
        $publish_set->siteName          = "";
        $publish_set->parentContainerId = "";

        $asset             = new \stdClass ();
        $asset->publishSet = $publish_set;
        return $asset;
    }
    
    public static function getReference() : \stdClass
    {
        $reference                      = new \stdClass ();
        $reference->name                = "";
        $reference->siteName            = "";
        $reference->parentFolderPath    = "";
        $reference->referencedAssetType = "";
        $reference->referencedAssetId   = "";

        $asset            = new \stdClass ();
        $asset->reference = $reference;
        return $asset;
    }
    
    public static function getRole() : \stdClass
    {
        $role                  = new \stdClass ();
        $role->name            = '';
        $role->roleType        = NULL;
        $role->globalAbilities = NULL;
        $role->siteAbilities   = NULL;
        
        $asset       = new \stdClass ();
        $asset->role = $role;
        return $asset;
    }
    
    public static function getSearchInformation() : \stdClass
    {
        $searchInformation               = new \stdClass ();
        $searchInformation->searchTerms  = "";
        $searchInformation->siteId       = "";
        $searchInformation->siteName     = "";
        $searchInformation->searchFields = new \stdClass ();
        $searchInformation->searchFields->searchField = "";
        $searchInformation->searchTypes  = new \stdClass ();
        $searchInformation->searchTypes->searchType = "";
        return $searchInformation;
    }

    public static function getSite() : \stdClass
    {
        $site                             = new \stdClass ();
        $site->name                       = "";
        $site->url                        = "";
        $site->recycleBinExpiration       = c\T::NEVER;
        $site->unpublishOnExpiration      = true;
        $site->linkCheckerEnabled         = true;
        $site->externalLinkCheckOnPublish = false;
        $site->inheritDataChecksEnabled   = true;
        $site->spellCheckEnabled          = true;
        $site->linkCheckEnabled           = true;
        $site->accessibilityCheckEnabled  = true;
        $site->inheritNamingRules         = true;

        $asset       = new \stdClass ();
        $asset->site = $site;
        return $asset;
    }
    
    public static function getStructuredDataNode() : \stdClass
    {
        $sdn                      = new \stdClass ();
        $sdn->type                = "";
        $sdn->identifier          = "";
        $sdn->structuredDataNodes = NULL;
        $sdn->text                = NULL;
        $sdn->assetType           = NULL;
        $sdn->blockId             = NULL;
        $sdn->blockPath           = NULL;
        $sdn->fileId              = NULL;
        $sdn->filePath            = NULL;
        $sdn->pageId              = NULL;
        $sdn->pagePath            = NULL;
        $sdn->symlinkId           = NULL;
        $sdn->symlinkPath         = NULL;
        $sdn->recycled            = false;
        return $sdn;
    }
    
    public static function getSymlink() : \stdClass
    {
        $symlink                   = new \stdClass ();
        $symlink->name             = "";
        $symlink->siteName         = "";
        $symlink->parentFolderPath = "";
        $symlink->linkURL          = "";

        $asset          = new \stdClass ();
        $asset->symlink = $symlink;
        return $asset;
    }
    
    public static function getTemplate() : \stdClass
    {
        $template                   = new \stdClass ();
        $template->name             = "";
        $template->siteName         = "";
        $template->parentFolderPath = "";
        $template->xml              = "";

        $asset           = new \stdClass ();
        $asset->template = $template;
        return $asset;
    }
    
    public static function getTextBlock() : \stdClass
    {
        $text_block                   = new \stdClass ();
        $text_block->name             = "";
        $text_block->siteName         = "";
        $text_block->parentFolderPath = "";
        $text_block->text             = "";

        $asset            = new \stdClass ();
        $asset->textBlock = $text_block;
        return $asset;
    }
    
    public static function getTwitterConnector() : \stdClass
    {
        $connector                   = new \stdClass ();
        $connector->name             = "";
        $connector->siteName         = "";
        $connector->parentFolderPath = "";
        $connector->destinationId    = "";

        $asset                   = new \stdClass ();
        $asset->twitterConnector = $connector;
        return $asset;
    }
    
    public static function getTwitterFeedBlock() : \stdClass
    {
        $block                   = new \stdClass ();
        $block->name             = "";
        $block->siteName         = "";
        $block->parentFolderPath = "";
        $block->accountName      = "chanw64";
        $block->searchString     = "Velocity";
        $block->maxResults       = 1;
        $block->useDefaultStyle  = true;
        $block->excludeJQuery    = true;
        $block->queryType        = "user-only";

        $asset                   = new \stdClass ();
        $asset->twitterFeedBlock = $block;
        return $asset;
    }
    
    public static function getUser() : \stdClass
    {
        $user           = new \stdClass ();
        $user->username = "";
        $user->authType = c\T::NORMAL;

        $asset       = new \stdClass ();
        $asset->user = $user;
        return $asset;
    }
    
    public static function getWordPressConnector() : \stdClass
    {
        $connector                   = new \stdClass ();
        $connector->name             = "";
        $connector->siteName         = "";
        $connector->parentFolderPath = "";
        $connector->url              = "";

        $asset                     = new \stdClass ();
        $asset->wordPressConnector = $connector;
        return $asset;
    }
    
    public static function getWorkflowDefinition() : \stdClass
    {
        $wd                      = new \stdClass ();
        $wd->name                = "";
        $wd->parentContainerPath = "";
        $wd->siteName            = "";
        $wd->namingBehavior      = "";
        $wd->xml                 = "";

        $asset                     = new \stdClass ();
        $asset->workflowDefinition = $wd;
        return $asset;
    }
    
    public static function getXmlBlock() : \stdClass
    {
        $xml_block                   = new \stdClass ();
        $xml_block->name             = "";
        $xml_block->siteName         = "";
        $xml_block->parentFolderPath = "";
        $xml_block->xml              = "";

        $asset            = new \stdClass ();
        $asset->xmlBlock = $xml_block;
        return $asset;
    }
    
    public static function getXhtmlPage() : \stdClass
    {
        $page                        = new \stdClass ();
        $page->name                  = "";
        $page->siteName              = "";
        $page->parentFolderPath      = "";
        $page->xhtml                 = "";
        $page->contentTypePath       = "";

        $asset       = new \stdClass ();
        $asset->page = $page;
        return $asset;
    }
}
?>