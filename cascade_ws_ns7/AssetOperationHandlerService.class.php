<?php
/**
  Author: Wing Ming Chan
  Copyright (c) 2017 Wing Ming Chan <chanw@upstate.edu>
  MIT Licensed
  Modification history:
   6/11/2017 Started adding WSDL.
   9/2/2016 Changed checkOut so that it returns the id of the working copy.
   8/15/2016 Added comments to work with ReflectionUtility.
   7/6/2015 Added getPreferences, readPreferences, and editPreferences.
   6/23/2015 Reverted the signature of performWorkflowTransition.
   5/26/2015 Added namespace
   5/21/2015 Added more comments
   5/5/2015 Added type hints to several methods
   12/10/2014 Fixed a bug in createId
   10/5/2014 Added getAsset
   8/15/2014 Modified createId to take care of whitespace and /
   7/18/2014 Modified createId to take care of assets in Global
   7/14/2014 Added getUrl
   6/6/2014 Fixed a bug in publish and unpublish
   4/17/2014 Modified the signature of retrieve so that the property can be empty
   3/24/2014 Modified createId to throw exceptions and added isHexString
   2/26/2014 Removed workflowConfiguration from property, and twitter feed block from property and type
   2/24/2014 Fixed a typo in the Property class
   1/8/2014 Changed all property strings to constants, added the $types array and a getType method
   10/30/2013 Fixed a bug in __call
   10/29/2013 Added storeResults
   10/28/2013 Added/modified all documentation comments
   10/26/2013 Added retrieve
   10/25/2013 Added the enhanced __call method to generate read and get
   10/21/2013 Added all operation methods
 */
namespace cascade_ws_AOHS;

use cascade_ws_constants as c;
use cascade_ws_utility   as u;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_exception as e;

/**
<documentation>
<description><h2>Introduction</h2>
<p>This class encapsulates the WSDL URL, the authentication object, and the SoapClient object, 
and provides services of all operations defined in the WSDL.</p>
<p>Operations:</p>
<pre>
&lt;complexType name="operation">
  &lt;choice>
    &lt;element name="create" type="impl:create"/>
    &lt;element name="delete" type="impl:delete"/>
    &lt;element name="edit" type="impl:edit"/>
    &lt;element name="move" type="impl:move"/>
    &lt;element name="publish" type="impl:publish"/>
    &lt;element name="read" type="impl:read"/>
    &lt;element name="readAccessRights" type="impl:readAccessRights"/>
    &lt;element name="editAccessRights" type="impl:editAccessRights"/>
    &lt;element name="readWorkflowSettings" type="impl:readWorkflowSettings"/>
    &lt;element name="editWorkflowSettings" type="impl:editWorkflowSettings"/>
    &lt;element name="listSubscribers" type="impl:listSubscribers"/>
    &lt;element name="checkOut" type="impl:checkOut"/>
    &lt;element name="checkIn" type="impl:checkIn"/>
    &lt;element name="copy" type="impl:copy"/>
    &lt;element name="siteCopy" type="impl:siteCopy"/>
  &lt;/choice>
&lt;/complexType>

&lt;complexType name="operationResult">
  &lt;sequence>
    &lt;element maxOccurs="1" name="success" type="xsd:string"/>
    &lt;element maxOccurs="1" name="message" nillable="true" type="xsd:string"/>
  &lt;/sequence>
&lt;/complexType>
</pre>
<p>Messages:</p>
<pre>&lt;wsdl:message name="batchRequest">
  &lt;wsdl:part element="impl:batch" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="batchResponse">
  &lt;wsdl:part element="impl:batchResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="createRequest">
  &lt;wsdl:part element="impl:create" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="createResponse">
  &lt;wsdl:part element="impl:createResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="deleteRequest">
  &lt;wsdl:part element="impl:delete" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="deleteResponse">
  &lt;wsdl:part element="impl:deleteResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="editRequest">
  &lt;wsdl:part element="impl:edit" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="editResponse">
  &lt;wsdl:part element="impl:editResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="publishRequest">
  &lt;wsdl:part element="impl:publish" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="publishResponse">
  &lt;wsdl:part element="impl:publishResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readRequest">
  &lt;wsdl:part element="impl:read" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readResponse">
  &lt;wsdl:part element="impl:readResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="searchRequest">
  &lt;wsdl:part element="impl:search" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="searchResponse">
  &lt;wsdl:part element="impl:searchResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readAccessRightsRequest">
  &lt;wsdl:part element="impl:readAccessRights" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readAccessRightsResponse">
  &lt;wsdl:part element="impl:readAccessRightsResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="editAccessRightsRequest">
  &lt;wsdl:part element="impl:editAccessRights" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="editAccessRightsResponse">
  &lt;wsdl:part element="impl:editAccessRightsResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readWorkflowSettingsRequest">
  &lt;wsdl:part element="impl:readWorkflowSettings" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readWorkflowSettingsResponse">
  &lt;wsdl:part element="impl:readWorkflowSettingsResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="editWorkflowSettingsRequest">
  &lt;wsdl:part element="impl:editWorkflowSettings" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="editWorkflowSettingsResponse">
  &lt;wsdl:part element="impl:editWorkflowSettingsResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="listSubscribersRequest">
  &lt;wsdl:part element="impl:listSubscribers" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="listSubscribersResponse">
  &lt;wsdl:part element="impl:listSubscribersResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="listEditorConfigurationsRequest">
  &lt;wsdl:part element="impl:listEditorConfigurations" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="listEditorConfigurationsResponse">
  &lt;wsdl:part element="impl:listEditorConfigurationsResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="listMessagesRequest">
  &lt;wsdl:part element="impl:listMessages" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="listMessagesResponse">
  &lt;wsdl:part element="impl:listMessagesResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="markMessageRequest">
  &lt;wsdl:part element="impl:markMessage" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="markMessageResponse">
  &lt;wsdl:part element="impl:markMessageResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="deleteMessageRequest">
  &lt;wsdl:part element="impl:deleteMessage" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="deleteMessageResponse">
  &lt;wsdl:part element="impl:deleteMessageResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="sendMessageRequest">
  &lt;wsdl:part element="impl:sendMessage" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="sendMessageResponse">
  &lt;wsdl:part element="impl:sendMessageResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="checkOutRequest">
  &lt;wsdl:part element="impl:checkOut" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="checkOutResponse">
  &lt;wsdl:part element="impl:checkOutResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="checkInRequest">
  &lt;wsdl:part element="impl:checkIn" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="checkInResponse">
  &lt;wsdl:part element="impl:checkInResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="copyRequest">
  &lt;wsdl:part element="impl:copy" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="copyResponse">
  &lt;wsdl:part element="impl:copyResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="siteCopyRequest">
  &lt;wsdl:part element="impl:siteCopy" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="siteCopyResponse">
  &lt;wsdl:part element="impl:siteCopyResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="listSitesRequest">
  &lt;wsdl:part element="impl:listSites" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="listSitesResponse">
  &lt;wsdl:part element="impl:listSitesResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="moveRequest">
  &lt;wsdl:part element="impl:move" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="moveResponse">
  &lt;wsdl:part element="impl:moveResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readWorkflowInformationRequest">
  &lt;wsdl:part element="impl:readWorkflowInformation" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readWorkflowInformationResponse">
  &lt;wsdl:part element="impl:readWorkflowInformationResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readAuditsRequest">
  &lt;wsdl:part element="impl:readAudits" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readAuditsResponse">
  &lt;wsdl:part element="impl:readAuditsResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="performWorkflowTransitionRequest">
  &lt;wsdl:part element="impl:performWorkflowTransition" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="performWorkflowTransitionResponse">
  &lt;wsdl:part element="impl:performWorkflowTransitionResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readPreferencesRequest">
  &lt;wsdl:part element="impl:readPreferences" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="readPreferencesResponse">
  &lt;wsdl:part element="impl:readPreferencesResponse" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="editPreferenceRequest">
  &lt;wsdl:part element="impl:editPreference" name="parameters"/>
&lt;/wsdl:message>
&lt;wsdl:message name="editPreferenceResponse">
  &lt;wsdl:part element="impl:editPreferenceResponse" name="parameters"/>
&lt;/wsdl:message>
</pre>
<p>Port types:</p>
<pre>
&lt;wsdl:portType name="AssetOperationHandler">
  &lt;wsdl:operation name="delete">
    &lt;wsdl:input message="impl:deleteRequest" name="deleteRequest"/>
    &lt;wsdl:output message="impl:deleteResponse" name="deleteResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="create">
    &lt;wsdl:input message="impl:createRequest" name="createRequest"/>
    &lt;wsdl:output message="impl:createResponse" name="createResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="publish">
    &lt;wsdl:input message="impl:publishRequest" name="publishRequest"/>
    &lt;wsdl:output message="impl:publishResponse" name="publishResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="edit">
    &lt;wsdl:input message="impl:editRequest" name="editRequest"/>
    &lt;wsdl:output message="impl:editResponse" name="editResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="batch">
    &lt;wsdl:input message="impl:batchRequest" name="batchRequest"/>
    &lt;wsdl:output message="impl:batchResponse" name="batchResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="read">
    &lt;wsdl:input message="impl:readRequest" name="readRequest"/>
    &lt;wsdl:output message="impl:readResponse" name="readResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="search">
    &lt;wsdl:input message="impl:searchRequest" name="searchRequest"/>
    &lt;wsdl:output message="impl:searchResponse" name="searchResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="readAccessRights">
    &lt;wsdl:input message="impl:readAccessRightsRequest" name="readAccessRightsRequest"/>
    &lt;wsdl:output message="impl:readAccessRightsResponse" name="readAccessRightsResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="editAccessRights">
    &lt;wsdl:input message="impl:editAccessRightsRequest" name="editAccessRightsRequest"/>
    &lt;wsdl:output message="impl:editAccessRightsResponse" name="editAccessRightsResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="readWorkflowSettings">
    &lt;wsdl:input message="impl:readWorkflowSettingsRequest" name="readWorkflowSettingsRequest"/>
    &lt;wsdl:output message="impl:readWorkflowSettingsResponse" name="readWorkflowSettingsResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="editWorkflowSettings">
    &lt;wsdl:input message="impl:editWorkflowSettingsRequest" name="editWorkflowSettingsRequest"/>
    &lt;wsdl:output message="impl:editWorkflowSettingsResponse" name="editWorkflowSettingsResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="listSubscribers">
    &lt;wsdl:input message="impl:listSubscribersRequest" name="listSubscribersRequest"/>
    &lt;wsdl:output message="impl:listSubscribersResponse" name="listSubscribersResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="listEditorConfigurations">
    &lt;wsdl:input message="impl:listEditorConfigurationsRequest" name="listEditorConfigurationsRequest"/>
    &lt;wsdl:output message="impl:listEditorConfigurationsResponse" name="listEditorConfigurationsResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="listMessages">
    &lt;wsdl:input message="impl:listMessagesRequest" name="listMessagesRequest"/>
    &lt;wsdl:output message="impl:listMessagesResponse" name="listMessagesResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="markMessage">
    &lt;wsdl:input message="impl:markMessageRequest" name="markMessageRequest"/>
    &lt;wsdl:output message="impl:markMessageResponse" name="markMessageResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="deleteMessage">
    &lt;wsdl:input message="impl:deleteMessageRequest" name="deleteMessageRequest"/>
    &lt;wsdl:output message="impl:deleteMessageResponse" name="deleteMessageResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="sendMessage">
    &lt;wsdl:input message="impl:sendMessageRequest" name="sendMessageRequest"/>
    &lt;wsdl:output message="impl:sendMessageResponse" name="sendMessageResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="checkOut">
    &lt;wsdl:input message="impl:checkOutRequest" name="checkOutRequest"/>
    &lt;wsdl:output message="impl:checkOutResponse" name="checkOutResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="checkIn">
    &lt;wsdl:input message="impl:checkInRequest" name="checkInRequest"/>
    &lt;wsdl:output message="impl:checkInResponse" name="checkInResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="copy">
    &lt;wsdl:input message="impl:copyRequest" name="copyRequest"/>
    &lt;wsdl:output message="impl:copyResponse" name="copyResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="siteCopy">
    &lt;wsdl:input message="impl:siteCopyRequest" name="siteCopyRequest"/>
    &lt;wsdl:output message="impl:siteCopyResponse" name="siteCopyResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="listSites">
    &lt;wsdl:input message="impl:listSitesRequest" name="listSitesRequest"/>
    &lt;wsdl:output message="impl:listSitesResponse" name="listSitesResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="move">
    &lt;wsdl:input message="impl:moveRequest" name="moveRequest"/>
    &lt;wsdl:output message="impl:moveResponse" name="moveResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="readWorkflowInformation">
    &lt;wsdl:input message="impl:readWorkflowInformationRequest" name="readWorkflowInformationRequest"/>
    &lt;wsdl:output message="impl:readWorkflowInformationResponse" name="readWorkflowInformationResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="readAudits">
    &lt;wsdl:input message="impl:readAuditsRequest" name="readAuditsRequest"/>
    &lt;wsdl:output message="impl:readAuditsResponse" name="readAuditsResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="performWorkflowTransition">
    &lt;wsdl:input message="impl:performWorkflowTransitionRequest" name="performWorkflowTransitionRequest"/>
    &lt;wsdl:output message="impl:performWorkflowTransitionResponse" name="performWorkflowTransitionResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="readPreferences">
    &lt;wsdl:input message="impl:readPreferencesRequest" name="readPreferencesRequest"/>
    &lt;wsdl:output message="impl:readPreferencesResponse" name="readPreferencesResponse"/>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="editPreference">
    &lt;wsdl:input message="impl:editPreferenceRequest" name="editPreferenceRequest"/>
    &lt;wsdl:output message="impl:editPreferenceResponse" name="editPreferenceResponse"/>
  &lt;/wsdl:operation>
&lt;/wsdl:portType>
</pre>
<p>Binding:</p>
<pre>&lt;wsdl:binding name="AssetOperationServiceSoapBinding" type="impl:AssetOperationHandler">
  &lt;wsdlsoap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>
  
  &lt;wsdl:operation name="batch">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="batchRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="batchResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="create">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="createRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="createResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="delete">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="deleteRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="deleteResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="edit">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="editRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="editResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="publish">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="publishRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="publishResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="read">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="readRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="readResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="search">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="searchRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="searchResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="readAccessRights">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="readAccessRightsRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="readAccessRightsResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="editAccessRights">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="editAccessRightsRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="editAccessRightsResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="readWorkflowSettings">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="readWorkflowSettingsRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="readWorkflowSettingsResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="editWorkflowSettings">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="editWorkflowSettingsRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="editWorkflowSettingsResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="listSubscribers">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="listSubscribersRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="listSubscribersResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="listEditorConfigurations">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="listEditorConfigurationsRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="listEditorConfigurationsResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="listMessages">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="listMessagesRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="listMessagesResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="markMessage">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="markMessageRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="markMessageResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="deleteMessage">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="deleteMessageRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="deleteMessageResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="sendMessage">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="sendMessageRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="sendMessageResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="checkOut">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="checkOutRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="checkOutResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="checkIn">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="checkInRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="checkInResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="copy">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="copyRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="copyResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="siteCopy">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="siteCopyRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="siteCopyResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="listSites">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="listSitesRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="listSitesResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="move">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="moveRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="moveResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="readWorkflowInformation">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="readWorkflowInformationRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="readWorkflowInformationResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="readAudits">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="readAuditsRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="readAuditsResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="performWorkflowTransition">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="performWorkflowTransitionRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="performWorkflowTransitionResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="readPreferences">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="readPreferencesRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="readPreferencesResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
  &lt;wsdl:operation name="editPreference">
    &lt;wsdlsoap:operation soapAction=""/>
    &lt;wsdl:input name="editPreferenceRequest">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:input>
    &lt;wsdl:output name="editPreferenceResponse">
      &lt;wsdlsoap:body use="literal"/>
    &lt;/wsdl:output>
  &lt;/wsdl:operation>
&lt;/wsdl:binding>
</pre>
</description>
<postscript><h2>Test Code</h2><ul><li><a href="https://github.com/wingmingchan/php-cascade-ws-ns-examples/tree/master/working-with-AssetOperationHandlerService">working-with-AssetOperationHandlerService</a></li></ul></postscript>
</documentation>
*/
class AssetOperationHandlerService
{
    const DEBUG = false;
    const DUMP  = false;
    const NAME_SPACE = "cascade_ws_AOHS";
/**
The constructor.
@param string $url The url of the WSDL
@param stdClass $auth The authentication object
@throws ServerException if the server connection cannot be established
<documentation><description><p>The constructor.</p></description>
<example>$service = new aohs\AssetOperationHandlerService( $wsdl, $auth );</example>
<return-type>void</return-type></documentation>
*/
    public function __construct( string $url, \stdClass $auth )
    {
        $this->url            = $url;
        $this->auth           = $auth;
        $this->message        = '';
        $this->success        = '';
        $this->createdAssetId = '';
        $this->lastRequest    = '';
        $this->lastResponse   = '';
        
        foreach( $this->properties as $property )
        {
            // turn a property name like 'publishSet' to 'PublishSet'
            $property = ucwords( $property );
            // populate the two arrays for dynamic generation of methods
            // attach the prefixes 'read' and 'get'
            $this->read_methods[] = 'read' . $property;
            $this->get_methods[]  = 'get'  . $property;
        }
        
        try
        {
            $this->soapClient = new \SoapClient( $this->url, array( 'trace' => 1 ) );
        }
        catch( \Exception $e )
        {
            throw new e\ServerException( S_SPAN . $e->getMessage() . E_SPAN );
        }
        catch( \Error $er )
        {
            throw new e\ServerException( S_SPAN . $er->getMessage() . E_SPAN );
        }
    }
    
/**
Dynamically generates the read and get methods.
@param string $func The function name
@param array $params The parameters fed into the function
@method stdClass getAssetFactory() Returns the asset factory read
@method readAssetFactory( stdClass ) Reads an asset factory
<documentation><description><p>Dynamically generates the read and get methods.</p></description>
<example></example>
<return-type>mixed</return-type></documentation>
*/
    function __call( string $func, array $params )
    {
        $property = "";
        // derive the property name from method name
        if( strpos( $func, 'read' ) === 0 )
            $property = substr( $func, 4 );
        else if( strpos( $func, 'get' ) === 0 )
            $property = substr( $func, 3 );
        
        $property = ucwords( $property );
        
        // read methods
        if( in_array( $func, $this->read_methods ) )
        {
            $read_param = new \stdClass();
            $read_param->authentication = $this->auth;
            $read_param->identifier     = $params[ 0 ];
    
            $this->reply = $this->soapClient->read( $read_param );
        
            if( ( $this->reply->readReturn->success == 'true' ) && 
                  isset( $this->reply->readReturn->asset->$property ) )
            {
                // store the property
                $this->read_assets[ $property ] = $this->reply->readReturn->asset->$property; 
            }
   
            $this->storeResults( $this->reply->readReturn );
        }
        // get methods
        else if( in_array( $func, $this->get_methods ) )
        {
            // could be NULL
            return $this->read_assets[ $property ];
        }
    }
    
/**
Batch-executes the operations.
@param array $operations The array of operations
<documentation><description><p>Batch-executes the operations.</p>
<p>batch:</p>
<pre>
&lt;element name="batch">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" type="impl:authentication"/>
      &lt;element maxOccurs="unbounded" name="operation" type="impl:operation"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="batchResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="unbounded" name="batchReturn" type="impl:batchResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="batchResult">
  &lt;choice>
    &lt;element maxOccurs="1" minOccurs="1" name="operationResult" type="impl:operationResult"/>
    &lt;element maxOccurs="1" minOccurs="1" name="checkOutResult" type="impl:checkOutResult"/>
    &lt;element maxOccurs="1" minOccurs="1" name="createResult" type="impl:createResult"/>
    &lt;element maxOccurs="1" minOccurs="1" name="listMessagesResult" type="impl:listMessagesResult"/>
    &lt;element maxOccurs="1" minOccurs="1" name="readResult" type="impl:readResult"/>
    &lt;element maxOccurs="1" minOccurs="1" name="readAccessRightsResult" type="impl:readAccessRightsResult"/>
    &lt;element maxOccurs="1" minOccurs="1" name="readWorkflowSettingsResult" type="impl:readWorkflowSettingsResult"/>
    &lt;element maxOccurs="1" minOccurs="1" name="readAuditsResult" type="impl:readAuditsResult"/>
    &lt;element maxOccurs="1" minOccurs="1" name="listSubscribersResult" type="impl:listSubscribersResult"/>
    &lt;element maxOccurs="1" minOccurs="1" name="searchResult" type="impl:searchResult"/>
    &lt;element maxOccurs="1" minOccurs="1" name="readWorkflowInformationResult" type="impl:readWorkflowInformationResult"/>
  &lt;/choice>
&lt;/complexType>
</pre>
</description>
<example>$paths = array( 
             "/_cascade/blocks/code/text-block", 
             "_cascade/blocks/code/ajax-read-profile-php" );

$operations = array();

foreach( $paths as $path )
{
    $id        = $service->createId( a\TextBlock::TYPE, $path, "cascade-admin" );
    $operation = new \stdClass();
    $read_op   = new \stdClass();
    
    $read_op->identifier = $id;
    $operation->read     = $read_op;
    $operations[]        = $operation;
}

try
{
    $service->batch( $operations );
    u\DebugUtility::dump( $service->getReply()->batchReturn );
}</example>
<return-type>void</return-type></documentation>
*/
    function batch( array $operations )
    {
        $batch_param                 = new \stdClass();
        $batch_param->authentication = $this->auth;
        $batch_param->operation      = $operations;
        
        $this->reply = $this->soapClient->batch( $batch_param );
        // the returned object is an array
        $this->storeResults();
    }
/**
Checks in an asset with the given identifier.
@param stdClass $identifier The identifier of the asset to be checked in
@param string   $comments The comments to be added
<documentation><description><p>Checks in an asset with the given identifier.</p>
<p>checkIn:</p>
<pre>
&lt;element name="checkIn">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
      &lt;element maxOccurs="1" minOccurs="1" name="comments" nillable="false" type="xsd:string"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="checkInResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="checkInReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="checkIn">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
  &lt;/sequence>
&lt;/complexType></pre>
</description>
<example>$path = "/files/AssetOperationHandlerService.class.php.zip";
$id = $service->createId( a\File::TYPE, $path, "cascade-admin" );
$service->checkIn( $id, 'Testing the checkIn method.' );
</example>
<return-type>void</return-type></documentation>
*/
    function checkIn( \stdClass $identifier, string $comments='' )
    {
        $checkin_param                 = new \stdClass();
        $checkin_param->authentication = $this->auth;
        $checkin_param->identifier     = $identifier;
        $checkin_param->comments       = $comments;
        
        $this->reply = $this->soapClient->checkIn( $checkin_param );
        $this->storeResults( $this->reply->checkInReturn );
    }
/**
Checks out an asset with the given identifier.
@param stdClass $identifier The identifier of the asset to be checked out
<documentation><description><p>Checks out an asset with the given identifier.</p>
<p>checkOut:</p>
<pre>&lt;element name="checkOut">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="checkOutResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="checkOutReturn" type="impl:checkOutResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="checkOut">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="checkOutResult">
  &lt;complexContent>
    &lt;extension base="impl:operationResult">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="0" name="workingCopyIdentifier" nillable="true" type="impl:identifier"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>
</pre>
</description>
<example>$path = "/files/AssetOperationHandlerService.class.php.zip";
$id = $service->createId( a\File::TYPE, $path, "cascade-admin" );
$service->checkOut( $id );
</example>
<return-type>string</return-type></documentation>
*/
    function checkOut( \stdClass $identifier )
    {
        $checkout_param                 = new \stdClass();
        $checkout_param->authentication = $this->auth;
        $checkout_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->checkOut( $checkout_param );
        $this->storeResults( $this->reply->checkOutReturn );
        
        if( $this->reply->checkOutReturn->success == "true" &&
            isset( $this->reply->checkOutReturn->workingCopyIdentifier ) &&
            !is_null( $this->reply->checkOutReturn->workingCopyIdentifier->id )  )
            return $this->reply->checkOutReturn->workingCopyIdentifier->id;
        else
            return "";
    }
/**
Copies the asset with the given identifier.
@param stdClass $identifier The identifier of the object to be copied
@param stdClass $newIdentifier The new identifier of the new object
@param string   $newName The new name assigned to the new object
@param bool     $doWorkflow Whether to do any workflow
<documentation><description><p>Copies the asset with the given identifier.</p>
<p>copy:</p>
<pre>&lt;element name="copy">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
      &lt;element maxOccurs="1" minOccurs="1" name="copyParameters" nillable="false" type="impl:copyParameters"/>
      &lt;element maxOccurs="1" minOccurs="0" name="workflowConfiguration" nillable="false" type="impl:workflow-configuration"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="copyResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="copyReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="copy">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
    &lt;element maxOccurs="1" minOccurs="1" name="copyParameters" nillable="false" type="impl:copyParameters"/>
    &lt;element maxOccurs="1" minOccurs="0" name="workflowConfiguration" nillable="false" type="impl:workflow-configuration"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="copyParameters">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="destinationContainerIdentifier" nillable="false" type="impl:identifier"/>
    &lt;element maxOccurs="1" minOccurs="1" name="doWorkflow" nillable="false" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="1" name="newName" nillable="false" type="xsd:string"/>
  &lt;/sequence>
&lt;/complexType></pre>
</description>
<example>// the block to be copy
$block_id = $service->createId( a\TextBlock::TYPE, "_cascade/blocks/code/text-block", "cascade-admin" );
// the parent folder where the new block should be placed
$parent_id = $service->createId( a\Folder::TYPE, "_cascade/blocks/code", "cascade-admin" );
// new name for the copy
$new_name = "another-text-block";
// no workflow
$do_workflow = false;
$service->copy( $block_id, $parent_id, $new_name, $do_workflow );
</example>
<return-type>void</return-type></documentation>
*/
    public function copy( \stdClass $identifier, \stdClass $newIdentifier, string $newName, bool $doWorkflow ) 
    {
        $copy_params                 = new \stdClass();
        $copy_params->authentication = $this->auth;
        $copy_params->identifier     = $identifier;
        $copy_params->copyParameters = new \stdClass();
        $copy_params->copyParameters->destinationContainerIdentifier = $newIdentifier;
        $copy_params->copyParameters->newName                        = $newName;
        $copy_params->copyParameters->doWorkflow                     = $doWorkflow;
        
        $this->reply = $this->soapClient->copy( $copy_params );
        $this->storeResults( $this->reply->copyReturn );
    }
/**
Creates the asset.
@param stdClass $asset The asset to be created
@return string The ID of the newly created asset
<documentation><description><p>Creates the asset.</p>
<p>create:</p>
<pre>&lt;element name="create">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="authentication" type="impl:authentication"/>
      &lt;element name="asset" type="impl:asset"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="createResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="createReturn" type="impl:createResult"/>
  &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="create">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="asset" type="impl:asset"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="createResult">
  &lt;complexContent>
    &lt;extension base="impl:operationResult">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="1" name="createdAssetId" nillable="false" type="xsd:string"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>
</pre>
</description>
<example>// get the image data
$img_url     = "http://www.upstate.edu/scripts/faculty/thumbs/nadkarna.jpg";
$img_binary  = file_get_contents( $img_url );
// the folder where the file should be created
$parent_id   = '980d653f8b7f0856015997e4bb59f630';
$site_name   = 'cascade-admin';
$img_name    = 'nadkarna.jpg';
// create the asset
$asset       = new \stdClass();
$asset->file = $service->createFileWithParentIdSiteNameNameData( 
    $parent_id, $site_name, $img_name, $img_binary );
$service->create( $asset );    
</example>
<return-type>mixed</return-type></documentation>
*/
    public function create( \stdClass $asset )
    {
        $create_params                 = new \stdClass();
        $create_params->authentication = $this->auth;
        $create_params->asset          = $asset;
        
        $this->reply = $this->soapClient->create( $create_params );
        $this->storeResults( $this->reply->createReturn );
        
        return $this->reply->createReturn->createdAssetId;
    }
/**
Creates an id object for an asset.
@param string $type The type of the asset
@param string $id_path Either the id or the path of an asset
@param string $siteName The site name
@return stdClass The identifier
<documentation><description><p>Creates an id object for an asset.</p></description>
<example>$block_id = $service->createId( a\TextBlock::TYPE, "_cascade/blocks/code/text-block", "cascade-admin" );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function createId(
        string $type, string $id_path, string $site_name=NULL ) : \stdClass
    {
        if( !( is_string( $type ) && ( is_string( $id_path ) || is_int( $id_path ) ) ) )
            throw new e\UnacceptableValueException( "Only strings are accepted in createId." );
            
        $non_digital_id_types = array(
            c\T::GROUP, c\T::ROLE, c\T::SITE, c\T::USER
        );
        
        $id_path = trim( $id_path );
        
        if( strlen( $id_path ) > 1 )
        {
            $id_path = trim( $id_path );
            $id_path = trim( $id_path, '/' );
        }
    
        $identifier = new \stdClass();
        
        if( $this->isHexString( $id_path ) )
        {
            // if id string is passed in, ignore site name
            $identifier->id = $id_path;
        }
        else if( in_array( $type, $non_digital_id_types ) )
        {
            if( $type != c\T::SITE ) // not a site
            {
                $identifier->id = $id_path;
            }
            else // a site
            {
                $identifier->path       = new \stdClass();
                $identifier->path->path = $id_path;
            }
        }
        else if( u\StringUtility::startsWith( $id_path, "ROOT_" ) )
        {
            $identifier->id = $id_path;
        }
        // asset in Global
        else if( $site_name == NULL )
        {
            $identifier->path           = new \stdClass();
            $identifier->path->path     = $id_path;
            $identifier->path->siteName = $site_name;
        }
        else
        {
            if( trim( $site_name ) == "" )
            {
                throw new e\EmptyValueException( 
                    S_SPAN . c\M::EMPTY_SITE_NAME . E_SPAN );
            }
            $identifier->path           = new \stdClass();
            $identifier->path->path     = $id_path;
            $identifier->path->siteName = $site_name;
        }
        $identifier->type = $type;
        return $identifier;
    }
/**
Creates an id object for an asset.
@param string $id The id string of an asset
@param string $type The type of the asset
@return stdClass The identifier
<documentation><description><p>Creates an id object for an asset.</p></description>
<example>$block_id = $service->createIdWithIdType( "388fa7a58b7ffe83164c93149320e775", a\TextBlock::TYPE );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function createIdWithIdType( string $id, string $type ) : \stdClass
    {
        return $this->createId( $type, $id );
    }
/**
Creates an id object for an asset.
@param string $path The path and name of an asset
@param string $siteName The site name
@param string $type The type of the asset
@return stdClass The identifier
<documentation><description><p>Creates an id object for an asset.</p></description>
<example>$block_id = $service->createIdWithPathSiteNameType( "_cascade/blocks/code/text-block", "cascade-admin", a\TextBlock::TYPE );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function createIdWithPathSiteNameType( string $path, string $site_name, string $type ) : \stdClass
    {
        return $this->createId( $type, $path, $site_name );
    }
/**
Creates a file stdClass object.
@param string $parentFolderId the id object of the parent folder
@param string $siteName The site name
@param string $name The name of the file
@param binary $data The data of the file
@return stdClass The file object
<documentation><description><p>Creates a file stdClass object.</p></description>
<example>$asset->file = $service->createFileWithParentIdSiteNameNameData( 
    $parent_id, $site_name, $img_name, $img_binary );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function createFileWithParentIdSiteNameNameData(
         string $parentFolderId, string $siteName, string $name, $data ) : \stdClass
    {
        $file                 = new \stdClass();
        $file->parentFolderId = $parentFolderId;
        $file->siteName       = $siteName;
        $file->name           = $name;
        $file->data           = $data;
        return $file;
    }
/**
Deletes the asset with the given identifier.
@param stdClass $identifier The identifier of the object to be deleted
<documentation><description><p>Deletes the asset with the given identifier.</p>
<p>delete:</p>
<pre>
&lt;element name="delete">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="authentication" type="impl:authentication"/>
      &lt;element name="identifier" type="impl:identifier"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="deleteResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="deleteReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="delete">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="0" name="workflowConfiguration" type="impl:workflow-configuration"/>
    &lt;element maxOccurs="1" minOccurs="1" name="identifier" type="impl:identifier"/>
  &lt;/sequence>
&lt;/complexType>
</pre>
</description>
<example>$path = "/_cascade/blocks/code/text-block2";
$service->delete( $service->createId( a\TextBlock::TYPE, $path, "cascade-admin" ) );
</example>
<return-type>void</return-type></documentation>
*/
    public function delete( \stdClass $identifier )
    {
        $delete_params                 = new \stdClass();
        $delete_params->authentication = $this->auth;
        $delete_params->identifier     = $identifier;
        
        $this->reply = $this->soapClient->delete( $delete_params );
        $this->storeResults( $this->reply->deleteReturn );
    }
/**
Deletes the message with the given identifier.
@param stdClass $identifier The identifier of the message to be deleted
<documentation><description><p>Deletes the message with the given identifier.</p>
<p>deleteMessage:</p>
<pre>&lt;element name="deleteMessage">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="deleteMessageResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="deleteMessageReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>
</pre>
</description>
<example>$mid = "9e10ae5b8b7ffe8364375ac78e212e42";
$service->deleteMessage( $service->createId( c\T::MESSAGE, $mid ) );
</example>
<return-type>void</return-type></documentation>
*/
    public function deleteMessage( \stdClass $identifier )
    {
        $delete_message_params                 = new \stdClass();
        $delete_message_params->authentication = $this->auth;
        $delete_message_params->identifier     = $identifier;
        
        $this->reply = $this->soapClient->deleteMessage( $delete_message_params );
        $this->storeResults( $this->reply->deleteMessageReturn );
    }
/**
Edits the given asset.
@param stdClass $asset The asset to be edited
<documentation><description><p>Edits the given asset.</p>
<p>edit:</p>
<pre>&lt;element name="edit">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="authentication" type="impl:authentication"/>
      &lt;element name="asset" type="impl:asset"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="editResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="editReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="edit">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="asset" type="impl:asset"/>
  &lt;/sequence>
&lt;/complexType></pre>
</description>
<example>$asset = new \stdClass();
$asset->xhtmlDataDefinitionBlock = $block;
$service->edit( $asset );
</example>
<return-type>void</return-type></documentation>
*/
    public function edit( \stdClass $asset )
    {
        $edit_params                 = new \stdClass();
        $edit_params->authentication = $this->auth;
        $edit_params->asset          = $asset;
        
        $this->reply = $this->soapClient->edit( $edit_params );
        $this->storeResults( $this->reply->editReturn );
    }
/**
Edits the given accessRightsInformation.
@param stdClass $accessRightsInformation the accessRightsInformation to be edited
@param bool     $applyToChildren Whether to apply the settings to children
<documentation><description><p>Edits the given accessRightsInformation.</p>
<p>editAccessRights:</p>
<pre>&lt;element name="editAccessRights">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="accessRightsInformation" nillable="false" type="impl:accessRightsInformation"/>
      &lt;element maxOccurs="1" minOccurs="0" name="applyToChildren" nillable="false" type="xsd:boolean"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="editAccessRightsResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="editAccessRightsReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="editAccessRights">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="accessRightsInformation" nillable="true" type="impl:accessRightsInformation"/>
    &lt;element maxOccurs="1" minOccurs="0" name="applyToChildren" nillable="false" type="xsd:boolean"/>
  &lt;/sequence>
&lt;/complexType>
</pre>
</description>
<example>$accessRightInfo->aclEntries->aclEntry = $aclEntries;
// false: do not apply to children
$service->editAccessRights( $accessRightInfo, false ); 
</example>
<return-type>void</return-type></documentation>
*/
    public function editAccessRights( \stdClass $accessRightsInformation, bool $applyToChildren )
    {
        $edit_params                          = new \stdClass();
        $edit_params->authentication          = $this->auth;
        $edit_params->accessRightsInformation = $accessRightsInformation;
        $edit_params->applyToChildren         = $applyToChildren;

        $this->reply = $this->soapClient->editAccessRights( $edit_params );
        $this->storeResults( $this->reply->editAccessRightsReturn );
    }
/**
Edits the preferences.
@param string $name The name of the preference
@param string $name The value of the preference
<documentation><description><p>Edits the preferences.</p>
<p>editPreference:</p>
<pre>&lt;element name="editPreference">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="preference" nillable="false" type="impl:preference"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="editPreferenceResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="editPreferenceReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="editPreferenceResult">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="preference" nillable="false" type="impl:preference"/>
  &lt;/sequence>
&lt;/complexType>
</pre></description>
<example>$service->editPreference( "system_pref_allow_font_assignment", "off" );</example>
<return-type>void</return-type></documentation>
*/
    public function editPreference( string $name, string $value ) 
    {
        $edit_preferences_param                    = new \stdClass();
        $edit_preferences_param->authentication    = $this->auth;
        $edit_preferences_param->preference        = new \stdClass();
        $edit_preferences_param->preference->name  = $name;
        $edit_preferences_param->preference->value = $value;
        
        $this->reply = $this->soapClient->editPreference( $edit_preferences_param );
        $this->storeResults( $this->reply->editPreferenceReturn );
    }
/**
Edits the preferences.
@param string $name The name of the preference
@param string $name The value of the preference
<documentation><description><p>An alias of <code>editPreference</code>.</p>
</description>
<return-type>void</return-type></documentation>
*/
    public function editPreferences( string $name, string $value ) 
    {
        $this->editPreference( $name, $value );
    }
/**
Edits the given workflowSettings.
@param stdClass $workflowSettings The workflowSettings to be edited
@param bool     $applyInheritWorkflowsToChildren Whether to apply inherited workflows to children
@param bool     $applyRequireWorkflowToChildren Whether to apply required workflows to children
<documentation><description><p>Edits the given workflowSettings.</p>
<p>editWorkflowSettings:</p>
<pre>&lt;element name="editWorkflowSettings">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="workflowSettings" nillable="false" type="impl:workflowSettings"/>
      &lt;element maxOccurs="1" minOccurs="0" name="applyInheritWorkflowsToChildren" nillable="false" type="xsd:boolean"/>
      &lt;element maxOccurs="1" minOccurs="0" name="applyRequireWorkflowToChildren" nillable="false" type="xsd:boolean"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="editWorkflowSettingsResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="editWorkflowSettingsReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="editWorkflowSettings">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="workflowSettings" nillable="true" type="impl:workflowSettings"/>
    &lt;element maxOccurs="1" minOccurs="0" name="applyInheritWorkflowsToChildren" nillable="false" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="applyRequireWorkflowToChildren" nillable="false" type="xsd:boolean"/>
  &lt;/sequence>
&lt;/complexType></pre>
</description>
<example>$service->editWorkflowSettings( $workflowSettings, false, false );</example>
<return-type>void</return-type></documentation>
*/
    public function editWorkflowSettings( 
        \stdClass $workflowSettings, bool $applyInheritWorkflowsToChildren, bool $applyRequireWorkflowToChildren )
    {
        $edit_params                   = new \stdClass();
        $edit_params->authentication   = $this->auth;
        $edit_params->workflowSettings = $workflowSettings;
        $edit_params->applyInheritWorkflowsToChildren = $applyInheritWorkflowsToChildren;
        $edit_params->applyRequireWorkflowToChildren  = $applyRequireWorkflowToChildren;
        
        $this->reply = $this->soapClient->editWorkflowSettings( $edit_params );
        $this->storeResults( $this->reply->editWorkflowSettingsReturn );
    }
/**
Creates an asset object, bridging this class and the Asset classes.
@param string $type The type of the asset
@param string $id_path Either the ID string or the path of the asset
@param binary $site_name The site name
@return a\Asset The asset object
@throw Exception if the asset cannot be retrieved
<documentation><description><p>Creates an asset object, bridging this class and the Asset classes.</p></description>
<example>$page = $service->getAsset( a\Page::TYPE, $page_id )</example>
<exception>NoSuchTypeException</exception>
<return-type>Asset</return-type></documentation>
*/
    public function getAsset( string $type, string $id_path, string $site_name=NULL ) : a\Asset
    {
        if( !in_array( $type, c\T::getTypeArray() ) )
            throw new e\NoSuchTypeException( 
                S_SPAN . "The type $type does not exist." . E_SPAN );
            
        $class_name = c\T::$type_class_name_map[ $type ]; // get class name
        $class_name = a\Asset::NAME_SPACE . "\\" . $class_name;
        
        try
        {
            return new $class_name( // call constructor
                $this, 
                $this->createId( $type, $id_path, $site_name ) );
        }
        catch( \Exception $e )
        {
            if( self::DEBUG && self::DUMP ) { u\DebugUtility::out( $e->getMessage() ); }
            throw $e;
        }        
    }
    
/**
Gets the audits object after the call of readAudits().
@return stdClass The audits object
<documentation><description><p>Gets the audits object after the call of readAudits().</p></description>
<example>u\DebugUtility::dump( $service->getAudits() );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getAudits() : \stdClass
    {
        return $this->audits;
    }
/**
Gets the ID of an asset newly created.
@return string The ID string
<documentation><description><p>Gets the ID of an asset newly created.</p></description>
<example>echo $service->getCreatedAssetId();</example>
<return-type>string</return-type></documentation>
*/
    public function getCreatedAssetId() : string
    {
        return $this->createdAssetId;
    }
/**
Gets the last request XML.
@return string The last request XML
<documentation><description><p>Gets the last request XML.</p></description>
<example>echo u\XMLUtility::replaceBrackets( $service->getLastRequest() );</example>
<return-type>string</return-type></documentation>
*/
    public function getLastRequest() : string
    {
        return $this->lastRequest;
    }
/**
Gets the last response.
@return string The last response
<documentation><description><p>Gets the last response.</p></description>
<example>echo S_PRE, u\XMLUtility::replaceBrackets( $service->getLastResponse() ), E_PRE;</example>
<return-type>string</return-type></documentation>
*/
    public function getLastResponse() : string
    {
        return $this->lastResponse;
    }
/**
Gets the messages object after the call of listMessages().
@return stdClass The listed messages
<documentation><description><p>Gets the messages object after the call of listMessages().</p></description>
<example>$service->listMessages();
u\DebugUtility::dump( $service->getListedMessages() );
</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getListedMessages() : \stdClass
    {
        return $this->listed_messages;
    }
/**
Gets the message after an operation.
@return string The message
<documentation><description><p>Gets the message after an operation.</p></description>
<example>echo $service->getMessage();</example>
<return-type>mixed</return-type></documentation>
*/
    public function getMessage()
    {
        return $this->message;
    }
/**
Gets the preferences after the call of readPreferences().
@return stdClass The preferences object
<documentation><description><p>Gets the preferences after the call of readPreferences().</p></description>
<example>$service->readPreferences();
u\DebugUtility::dump( $service->getPreferences() );</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getPreferences() : \stdClass
    {
        return $this->preferences;
    }
/**
Gets the accessRightInformation object after the call of readAccessRightInformation().
@return stdClass The accessRightsInformation object
<documentation><description><p>Gets the accessRightInformation object after the call of readAccessRightInformation().</p></description>
<example>$accessRightInfo = $service->getReadAccessRightInformation();</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getReadAccessRightInformation() : \stdClass
    {
        return $this->reply->readAccessRightsReturn->accessRightsInformation;
    }
/**
Gets the asset object after the call of read().
@return stdClass The asset read
<documentation><description><p>Gets the asset object after the call of read().</p></description>
<example>$container = $service->getReadAsset()->assetFactoryContainer;</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getReadAsset() : \stdClass
    {
        return $this->reply->readReturn->asset;
    }
/**
Gets the file object after the call of read().
@return stdClass The file read
<documentation><description><p>Gets the file object after the call of read().</p></description>
<example>$file = $service->getReadFile();</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getReadFile() : \stdClass
    {
        return $this->reply->readReturn->asset->file;
    }
/**
Gets the workflow object after the call of readWorkflow().
@return stdClass The workflow read
<documentation><description><p>Gets the workflow object after the call of readWorkflow().</p></description>
<example>$service->readWorkflowInformation( 
    $service->createId( a\Page::TYPE, $path, "cascade-admin" ) );
$workflow = $service->getReadWorkflow();</example>
<return-type>mixed</return-type></documentation>
*/
    public function getReadWorkflow()
    {
        return $this->reply->readWorkflowInformationReturn->workflow;
    }
/**
Gets the workflowSettings object after the call of readWorkflowSettings().
@return stdClass The workflowSettings object
<documentation><description><p></p></description>
<example>$service->readWorkflowSettings( 
    $service->createId( a\Folder::TYPE, "/", $site_name ) );
$workflowSettings = $service->getReadWorkflowSettings();
</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getReadWorkflowSettings() : \stdClass
    {
        return $this->reply->readWorkflowSettingsReturn->workflowSettings;
    }
/**
Gets the response object after an operation.
@return stdClass The response object
<documentation><description><p>Gets the workflowSettings object after the call of readWorkflowSettings().</p></description>
<example>$reply = $service->getReply();</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getReply() : \stdClass
    {
        return $this->reply;
    }
/**
Gets the searchMatches object after the call of search().
@return stdClass The searchMatches object
<documentation><description><p>Gets the searchMatches object after the call of search().</p></description>
<example>$service->search( $search_for );
if( is_null( $service->getSearchMatches()->match ) )
{
    // do something
}</example>
<return-type>stdClass</return-type></documentation>
*/
    public function getSearchMatches() : \stdClass
    {
        return $this->searchMatches;
    }
/**
Returns a bool after an operation.
@return string The string 'true' or 'false'
<documentation><description><p>Returns a bool after an operation indicating whether the search is successful.</p></description>
<example>if ( $service->getSuccess() )</example>
<return-type>bool</return-type></documentation>
*/
    public function getSuccess() : bool
    {
        return $this->success;
    }
/**
Gets the type of an asset.
@param string $id_string The 32-digit hex id string
@return string The type string
<documentation><description><p>Gets the type of an asset.</p></description>
<example>$id = "3896de848b7ffe83164c931422421045";
echo $service->getType( $id ), BR;
</example>
<return-type>string</return-type></documentation>
*/
    public function getType( string $id_string ) : string
    {
        $type_count = count( $this->types );
        
        for( $i = 0; $i < $type_count; $i++ )
        {
            $id = $this->createId( $this->types[ $i ], $id_string );
            $operation = new \stdClass();
            $read_op   = new \stdClass();
    
            $read_op->identifier = $id;
            $operation->read     = $read_op;
            $operations[]        = $operation;
        }
        
        $this->batch( $operations );
        
        $reply_array = $this->getReply()->batchReturn;
        
        for( $j = 0; $j < $type_count; $j++ )
        {
            if( $reply_array[ $j ]->readResult->success == 'true' )
            {
                foreach( c\T::$type_property_name_map as $type => $property )
                {
                    if( isset( $reply_array[ $j ]->readResult->asset->$property ) )
                        return $type;
                }
            }
        }
        
        return "The id does not match any asset type.";
    }
/**
Gets the WSDL URL string.
@return string The WSDL URL string
<documentation><description><p>Gets the WSDL URL string.</p></description>
<example>echo $service->getUrl(), BR;</example>
<return-type>string</return-type></documentation>
*/
    public function getUrl() : string
    {
        return $this->url;
    }
/**
Returns a bool indicating whether the string is a 32-digit hex string.
@param string $string The input string
@return bool Whether the input string is a hex string
<documentation><description><p>Returns a bool indicating whether the string is a 32-digit hex string.</p></description>
<example>if( $service->isHexString( $id ) )
    echo $service->getType( $id ), BR;</example>
<return-type>bool</return-type></documentation>
*/
    public function isHexString( string $string ) : bool
    {
        $pattern = "/[0-9a-f]{32}/";
        $matches = array();
        
        preg_match( $pattern, $string, $matches );
        
        if( isset( $matches[ 0 ] ) )
            return $matches[ 0 ] == $string;
        return false;
    }
/**
Returns true if an operation is successful.
@return bool The result of an operation
<documentation><description><p>Returns true if an operation is successful.</p></description>
<example>$service->readPreferences();
if( $service->isSuccessful() )
{
    // do something
}</example>
<return-type>bool</return-type></documentation>
*/
    public function isSuccessful() : bool
    {
        return $this->success == 'true';
    }
/**
<documentation><description><p>Lists editor configurations.</p>
<p>:</p>
<pre></pre>
</description>
<example></example>
<return-type>void</return-type></documentation>
*/
	public function listEditorConfigurations()
    {
        $list_editor_configurations_params                 = new \stdClass();
        $list_editor_configurations_params->authentication = $this->auth;
        
        $this->reply = $this->soapClient->listEditorConfigurations(
        	$list_editor_configurations_params );
        $this->storeResults( $this->reply->listEditorConfigurationsReturn );
    }
/**
Lists all messages.
<documentation><description><p>Lists all messages.</p>
<p>listMessages:</p>
<pre>&lt;element name="listMessages">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="listMessagesResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="listMessagesReturn" type="impl:listMessagesResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="listMessagesResult">
  &lt;complexContent>
    &lt;extension base="impl:operationResult">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="0" name="messages" nillable="true" type="impl:messagesList"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType></pre>
</description>
<example>$service->listMessages();</example>
<return-type>void</return-type></documentation>
*/
    public function listMessages()
    {
        $list_messages_params                 = new \stdClass();
        $list_messages_params->authentication = $this->auth;
        
        $this->reply = $this->soapClient->listMessages( $list_messages_params );
        $this->storeResults( $this->reply->listMessagesReturn );
        
        if( $this->isSuccessful() )
        {
            $this->listed_messages = $this->reply->listMessagesReturn->messages;
        }
    }
/**
Lists all sites.
<documentation><description><p>Lists all sites.</p>
<p>listSites:</p>
<pre>&lt;element name="listSites">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="listSitesResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="listSitesReturn" type="impl:listSitesResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="listSitesResult">
  &lt;complexContent>
    &lt;extension base="impl:operationResult">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="1" name="sites" type="impl:assetIdentifiers"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>
</pre>
</description>
<example>$service->listSites();</example>
<return-type>void</return-type></documentation>
*/
    public function listSites()
    {
        $list_sites_params                 = new \stdClass();
        $list_sites_params->authentication = $this->auth;
        
        $this->reply = $this->soapClient->listSites( $list_sites_params );
        $this->storeResults( $this->reply->listSitesReturn );
    }
/**
Lists all subscribers of an asset.
@param stdClass $identifier The identifier of the asset
<documentation><description><p>Lists all subscribers of an asset.</p>
<p>listSubscribers:</p>
<pre>&lt;element name="listSubscribers">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="authentication" type="impl:authentication"/>
      &lt;element name="identifier" type="impl:identifier"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="listSubscribersResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="listSubscribersReturn" type="impl:listSubscribersResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="listSubscribers">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="listSubscribersResult">
  &lt;complexContent>
    &lt;extension base="impl:operationResult">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="1" name="subscribers" type="impl:assetIdentifiers"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType></pre>
</description>
<example>$service->listSubscribers( 
    $service->createId( $type, $path, $site_name ) );</example>
<return-type>void</return-type></documentation>
*/
    public function listSubscribers( \stdClass $identifier )
    {
        $list_subscribers_params                 = new \stdClass();
        $list_subscribers_params->authentication = $this->auth;
        $list_subscribers_params->identifier     = $identifier;
        
        $this->reply = $this->soapClient->listSubscribers( $list_subscribers_params );
        $this->storeResults( $this->reply->listSubscribersReturn );
    }
/**
Marks a message as 'read' or 'unread'.
@param stdClass $identifier The identifier of the message
@param string   $markType The string 'read' or 'unread'
<documentation><description><p>Marks a message as 'read' or 'unread'.</p>
<p>markMessage:</p>
<pre>&lt;element name="markMessage">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
      &lt;element maxOccurs="1" minOccurs="1" name="markType" nillable="false" type="impl:message-mark-type"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="markMessageResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="markMessageReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element></pre>
</description>
<example>$service->markMessage( 
    $service->createIdWithIdType( $id, c\T::MESSAGE ), 
    c\T::UNREAD );
</example>
<return-type>void</return-type></documentation>
*/
    public function markMessage( \stdClass $identifier, string $markType )
    {
        $mark_message_params                 = new \stdClass();
        $mark_message_params->authentication = $this->auth;
        $mark_message_params->identifier     = $identifier;
        $mark_message_params->markType       = $markType;
        
        $this->reply = $this->soapClient->markMessage( $mark_message_params );
        $this->storeResults( $this->reply->markMessageReturn );
    }
/**
Moves the asset with the given identifier.
@param stdClass $identifier The identifier of the object to be moved
@param stdClass $newIdentifier The new container identifier
@param string   $newName The new name assigned to the object moved
@param bool     $doWorkflow Whether to do workflow
<documentation><description><p>Moves the asset with the given identifier.</p>
<p>move:</p>
<pre>&lt;element name="move">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
      &lt;element maxOccurs="1" minOccurs="1" name="moveParameters" nillable="false" type="impl:moveParameters"/>
      &lt;element maxOccurs="1" minOccurs="0" name="workflowConfiguration" nillable="false" type="impl:workflow-configuration"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="moveResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="moveReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="move">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
    &lt;element maxOccurs="1" minOccurs="1" name="moveParameters" nillable="false" type="impl:moveParameters"/>
    &lt;element maxOccurs="1" minOccurs="0" name="workflowConfiguration" nillable="false" type="impl:workflow-configuration"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="moveParameters">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="0" name="destinationContainerIdentifier" type="impl:identifier"/>
    &lt;element maxOccurs="1" minOccurs="1" name="doWorkflow" nillable="false" type="xsd:boolean"/>
    &lt;element maxOccurs="1" minOccurs="0" name="newName" type="xsd:string"/>
  &lt;/sequence>
&lt;/complexType>
</pre></description>
<example>$service->move( $block_id, $parent_id, $new_name, $do_workflow );</example>
<return-type>void</return-type></documentation>
*/
    function move( \stdClass $identifier, \stdClass $newIdentifier=NULL, string $newName="", bool $doWorkflow=false ) 
    {
        $move_params                 = new \stdClass();
        $move_params->authentication = $this->auth;
        $move_params->identifier     = $identifier;
        $move_params->moveParameters = new \stdClass();
        $move_params->moveParameters->destinationContainerIdentifier = $newIdentifier;
        $move_params->moveParameters->newName                        = $newName;
        $move_params->moveParameters->doWorkflow                     = $doWorkflow;
        
        $this->reply = $this->soapClient->move( $move_params );
        $this->storeResults( $this->reply->moveReturn );
    }
/**
Performs the workflow transition.
@param string   $workflowId The current workflow ID
@param string   $actionIdentifier The identifier of the action
@param string   $transitionComment The comments
<documentation><description><p>Performs the workflow transition.</p>
<p>performWorkflowTransition:</p>
<pre>&lt;element name="performWorkflowTransition">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="workflowTransitionInformation" nillable="false" type="impl:workflowTransitionInformation"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="performWorkflowTransitionResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="performWorkflowTransitionReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element></pre>
</description>
<example>$service->performWorkflowTransition( $id, $action, 'Testing' );</example>
<return-type>void</return-type></documentation>
*/
    public function performWorkflowTransition( 
         string $workflowId, string $actionIdentifier, string $transitionComment='' )
    {
        $workflowTransitionInformation                    = new \stdClass();
        $workflowTransitionInformation->workflowId        = $workflowId;
        $workflowTransitionInformation->actionIdentifier  = $actionIdentifier;
        $workflowTransitionInformation->transitionComment = $transitionComment;
        
        $transition_params                                = new \stdClass();
        $transition_params->authentication                = $this->auth;
        $transition_params->workflowTransitionInformation = $workflowTransitionInformation;
        
        $this->reply = $this->soapClient->performWorkflowTransition( $transition_params );
        $this->storeResults( $this->reply->performWorkflowTransitionReturn );
    }
/**
Prints the XML of the last request.
<documentation><description><p>Prints the XML of the last request.</p></description>
<example>$service->printLastRequest();</example>
<return-type>void</return-type></documentation>
*/
    public function printLastRequest()
    {
        print_r( $this->lastRequest );
    }
/**
Prints the XML of the last response.
<documentation><description><p>Prints the XML of the last response.</p></description>
<example>$service->printLastResponse();</example>
<return-type>void</return-type></documentation>
*/
    public function printLastResponse()
    {
        print_r( $this->lastResponse );
    }
/**
Publishes the asset with the given identifier.
@param stdClass $identifier The identifier of the object to be published
@param Destination $destination The destination(s) where the asset should be published
<documentation>
<description><p>Publishes the asset with the given identifier.</p>
<p>publish:</p>
<pre>
&lt;element name="publish">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="publishInformation" type="impl:publishInformation"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="publishResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="publishReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="publish">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="publishInformation" type="impl:publishInformation"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="publishInformation">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="identifier" type="impl:identifier"/>
    &lt;element maxOccurs="1" minOccurs="0" name="destinations" nillable="false" type="impl:assetIdentifiers"/>
    &lt;element maxOccurs="1" minOccurs="0" name="unpublish" nillable="true" type="xsd:boolean"/>
  &lt;/sequence>
&lt;/complexType>
</pre>
</description>
<example>$folder_path = "projects/web-services/reports";
$service->publish( $service->createId( a\Folder::TYPE, $folder_path, "cascade-admin" ) );</example>
<return-type>void</return-type>
</documentation>
*/
    public function publish( \stdClass $identifier, a\Destination $destination=NULL ) 
    {
        $publish_param = new \stdClass();
        $publish_info  = new \stdClass();
        $publish_param->authentication = $this->auth;
        $publish_info->identifier      = $identifier;
        
        if( isset( $destination ) )
        {
            if( is_array( $destination ) )
                $publish_info->destinations = $destination;
            else
                $publish_info->destinations = array( $destination );
        }
        
        $publish_info->unpublish           = false;
        $publish_param->publishInformation = $publish_info;
        
        $this->reply = $this->soapClient->publish( $publish_param );
        $this->storeResults( $this->reply->publishReturn );
    }
/**
Reads the asset with the given identifier.
@param stdClass $identifier The identifier of the object to be read
<documentation>
<description><p>Reads the asset with the given identifier.</p>
<p>read:</p>
<pre>
&lt;element name="read">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="authentication" type="impl:authentication"/>
      &lt;element name="identifier" type="impl:identifier"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="readResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="readReturn" type="impl:readResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="read">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="identifier" type="impl:identifier"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="readResult">
  &lt;complexContent>
    &lt;extension base="impl:operationResult">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="1" name="asset" type="impl:asset"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>
<pre>
</description>
<example>$service->read( 
    $service->createId( a\Folder::TYPE, $path, "cascade-admin" ) );</example>
<return-type>void</return-type>
</documentation>
*/
    public function read( \stdClass $identifier ) 
    {
        if( self::DEBUG ) { u\DebugUtility::dump( $identifier ); }
        
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->read( $read_param );
        $this->storeResults( $this->reply->readReturn );
    }
/**
Reads the access rights of the asset with the given identifier.
@param stdClass $identifier The identifier of the object to be read
<documentation><description><p>Reads the access rights of the asset with the given identifier.</p>
<p>readAccessRights:</p>
<pre>&lt;element name="readAccessRights">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="authentication" type="impl:authentication"/>
      &lt;element name="identifier" type="impl:identifier"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="readAccessRightsResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="readAccessRightsReturn" type="impl:readAccessRightsResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="readAccessRights">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="readAccessRightsResult">
  &lt;complexContent>
    &lt;extension base="impl:operationResult">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="1" name="accessRightsInformation" nillable="false" type="impl:accessRightsInformation"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>
</pre>
</description>
<example>$service->readAccessRights( 
    $service->createId( a\TextBlock::TYPE, $path, "cascade-admin" ) );
</example>
<return-type>void</return-type></documentation>
*/
    public function readAccessRights( \stdClass $identifier ) 
    {
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->readAccessRights( $read_param );
        $this->storeResults( $this->reply->readAccessRightsReturn );
    }
/**
Reads the audits of the asset with the given parameters.
@param stdClass $params The parameters of readAudits
<documentation><description><p>Reads the audits of the asset with the given parameters.</p>
<p>readAudits:</p>
<pre>&lt;element name="readAudits">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="auditParameters" nillable="false" type="impl:auditParameters"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="readAuditsResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="readAuditsReturn" type="impl:readAuditsResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>
</pre>
</description>
<example>$page_id = "980d85f48b7f0856015997e492c9b83b";
$audit_params = new \stdClass();
$audit_params->identifier = $service->createId( a\Page::TYPE, $page_id );
$audit_params->auditType  = c\T::EDIT;
$service->readAudits( $audit_params );
</example>
<return-type>void</return-type></documentation>
*/
    public function readAudits( \stdClass $params ) 
    {
        $read_audits_param                  = new \stdClass();
        $read_audits_param->authentication  = $this->auth;
        $read_audits_param->auditParameters = $params;
        
        $this->reply = $this->soapClient->readAudits( $read_audits_param );
        $this->storeResults( $this->reply->readAuditsReturn );
        $this->audits  = $this->reply->readAuditsReturn->audits;
    }
/**
Reads the preferences.
<documentation><description><p>Reads the preferences.</p>
<p>readPreferences:</p>
<pre>&lt;element name="readPreferences">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="readPreferencesResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="readPreferencesReturn" type="impl:readPreferencesResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="readPreferencesResult">
  &lt;complexContent>
    &lt;extension base="impl:operationResult">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="1" name="preferences" nillable="true" type="impl:preferencesList"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>
</pre>
</description>
<example>$service->readPreferences();</example>
<return-type>void</return-type></documentation>
*/
    public function readPreferences() 
    {
        $read_preferences_param                  = new \stdClass();
        $read_preferences_param->authentication  = $this->auth;
        
        $this->reply = $this->soapClient->readPreferences( $read_preferences_param );
        $this->storeResults( $this->reply->readPreferencesReturn );
        $this->preferences  = $this->reply->readPreferencesReturn->preferences;
    }
/**
Reads the workflow information associated with the given identifier.
@param stdClass $identifier The identifier of the object to be read
<documentation><description><p>Reads the workflow information associated with the given identifier.</p>
<p>readWorkflowInformation:</p>
<pre>&lt;element name="readWorkflowInformation">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="readWorkflowInformationResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="readWorkflowInformationReturn" type="impl:readWorkflowInformationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="readWorkflowInformationResult">
  &lt;complexContent>
    &lt;extension base="impl:operationResult">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="0" name="workflow" type="impl:workflow"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>
</pre>
</description>
<example>$path = '/projects/web-services/reports/creating-format';
$service->readWorkflowInformation( 
    $service->createId( a\Page::TYPE, $path, "cascade-admin" ) );</example>
<return-type>void</return-type></documentation>
*/
    public function readWorkflowInformation( \stdClass $identifier ) 
    {
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->readWorkflowInformation( $read_param );
        $this->storeResults( $this->reply->readWorkflowInformationReturn );
    }    
/**
Reads the workflow settings associated with the given identifier.
@param stdClass $identifier The identifier of the object to be read
<documentation><description><p>Reads the workflow settings associated with the given identifier.</p>
<p>readWorkflowSettings:</p>
<pre>&lt;element name="readWorkflowSettings">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="authentication" type="impl:authentication"/>
      &lt;element name="identifier" type="impl:identifier"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="readWorkflowSettingsResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="readWorkflowSettingsReturn" type="impl:readWorkflowSettingsResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="readWorkflowSettings">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="identifier" nillable="false" type="impl:identifier"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="readWorkflowSettingsResult">
  &lt;complexContent>
    &lt;extension base="impl:operationResult">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="1" name="workflowSettings" nillable="false" type="impl:workflowSettings"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>
</pre>
</description>
<example>$site_name = "cascade-admin";
$service->readWorkflowSettings( 
    $service->createId( a\Folder::TYPE, "/", $site_name ) );
</example>
<return-type>void</return-type></documentation>
*/
    public function readWorkflowSettings( \stdClass $identifier ) 
    {
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $identifier;
        
        $this->reply = $this->soapClient->readWorkflowSettings( $read_param );
        $this->storeResults( $this->reply->readWorkflowSettingsReturn );
    }
/**
Retrieves a property of an asset.
@param stdClass $id The id of the property
@param string $property The property name
@return stdClass The property or NULL
<documentation><description><p>Retrieves a property of an asset.</p></description>
<example>$page = $service->retrieve( $service->createId( a\Page::TYPE, $page_path, "cascade-admin" ) );</example>
<return-type>stdClass</return-type></documentation>
*/
    function retrieve( \stdClass $id, string $property="" )
    {
        if( $property == "" )
        {
            $property = c\T::$type_property_name_map[ $id->type ];
        }
        
        $read_param                 = new \stdClass();
        $read_param->authentication = $this->auth;
        $read_param->identifier     = $id;

        $this->reply = $this->soapClient->read( $read_param );
        $this->storeResults( $this->reply->readReturn );

        if( isset( $this->reply->readReturn->asset ) )
            return $this->reply->readReturn->asset->$property;
        return NULL;
    }
/**
Searches for some entity.
@param stdClass $searchInfo The searchInfo object
<documentation><description><p>Searches for some entity.</p>
<p>search:</p>
<pre>&lt;element name="search">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="authentication" type="impl:authentication"/>
      &lt;element name="searchInformation" type="impl:searchInformation"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="searchResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="searchReturn" type="impl:searchResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="searchResult">
  &lt;complexContent>
    &lt;extension base="impl:operationResult">
      &lt;sequence>
        &lt;element maxOccurs="1" minOccurs="1" name="matches" type="impl:search-matches"/>
      &lt;/sequence>
    &lt;/extension>
  &lt;/complexContent>
&lt;/complexType>

&lt;complexType name="search-matches">
  &lt;sequence>
    &lt;element maxOccurs="unbounded" minOccurs="0" name="match" type="impl:identifier"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="searchInformation">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="1" name="searchTerms" type="xsd:string"/>
    &lt;element maxOccurs="1" minOccurs="0" name="siteId" nillable="false" type="xsd:string"/>
    &lt;element maxOccurs="1" minOccurs="0" name="siteName" nillable="false" type="xsd:string"/>
    &lt;element maxOccurs="1" minOccurs="0" name="searchFields" nillable="false" type="impl:searchFields"/>
    &lt;element maxOccurs="1" minOccurs="0" name="searchTypes" nillable="false" type="impl:searchTypes"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="searchFields">
  &lt;sequence>
    &lt;element maxOccurs="unbounded" minOccurs="0" name="searchField" nillable="false" type="impl:searchFieldString"/>
  &lt;/sequence>
&lt;/complexType>

&lt;complexType name="searchTypes">
  &lt;sequence>
    &lt;element maxOccurs="unbounded" minOccurs="0" name="searchType" nillable="false" type="impl:entityTypeString"/>
  &lt;/sequence>
&lt;/complexType>

&lt;simpleType name="searchFieldString">
  &lt;restriction base="xsd:string">
    &lt;enumeration value="name"/>
    &lt;enumeration value="path"/>
    &lt;enumeration value="createdBy"/>
    &lt;enumeration value="modifiedBy"/>
    &lt;enumeration value="displayName"/>
    &lt;enumeration value="title"/>
    &lt;enumeration value="summary"/>
    &lt;enumeration value="teaser"/>
    &lt;enumeration value="keywords"/>
    &lt;enumeration value="description"/>
    &lt;enumeration value="author"/>
    &lt;enumeration value="blob"/>
    &lt;enumeration value="velocityFormatContent"/>
    &lt;enumeration value="xml"/>
    &lt;enumeration value="link"/>
  &lt;/restriction>
&lt;/simpleType></pre>
</description>
<example>$search_for               = new \stdClass();
$search_for->matchType    = c\T::MATCH_ANY;
$search_for->searchGroups = true;
$search_for->assetName    = $group;
$service->search( $search_for );</example>
<return-type>void</return-type></documentation>
*/
    public function search( \stdClass $searchInfo ) 
    {
        $search_info_param                    = new \stdClass();
        $search_info_param->authentication    = $this->auth;
        $search_info_param->searchInformation = $searchInfo;
        
        $this->reply = $this->soapClient->search( $search_info_param );
        $this->searchMatches = $this->reply->searchReturn->matches;
        $this->storeResults( $this->reply->searchReturn );
    }        
/**
Sends a message.
@param stdClass $message The message object to be sent
<documentation><description><p>Sends a message.</p>
<p>sendMessage:</p>
<pre>&lt;element name="sendMessage">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="1" name="message" nillable="false" type="impl:message"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="sendMessageResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="sendMessageReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>
</pre>
</description>
<example>$message          = new \stdClass();
$message->to      = 'test'; // a group
$message->from    = 'chanw';
$message->subject = 'test';
$message->body    = 'This is a test. This is only a test.';
$service->sendMessage( $message );
</example>
<return-type>void</return-type></documentation>
*/
    public function sendMessage( \stdClass $message ) 
    {
        $send_message_param                 = new \stdClass();
        $send_message_param->authentication = $this->auth;
        $send_message_param->message        = $message;
        
        $this->reply = $this->soapClient->sendMessage( $send_message_param );
        $this->storeResults( $this->reply->sendMessageReturn );
    }    
/**
Copies the site with the given identifier.
@param string $original_id The ID string of the site to be copied
@param string $original_name The name of the site to be copied
@param string $new_name The name assigned to the new site
<documentation><description><p>Copies the site with the given identifier.</p>
<p>siteCopy:</p>
<pre>&lt;element name="siteCopy">
  &lt;complexType>
    &lt;sequence>
      &lt;element maxOccurs="1" minOccurs="1" name="authentication" nillable="false" type="impl:authentication"/>
      &lt;element maxOccurs="1" minOccurs="0" name="originalSiteId" type="xsd:string"/>
      &lt;element maxOccurs="1" minOccurs="0" name="originalSiteName" type="xsd:string"/>
      &lt;element maxOccurs="1" minOccurs="1" name="newSiteName" nillable="false" type="xsd:string"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;element name="siteCopyResponse">
  &lt;complexType>
    &lt;sequence>
      &lt;element name="siteCopyReturn" type="impl:operationResult"/>
    &lt;/sequence>
  &lt;/complexType>
&lt;/element>

&lt;complexType name="siteCopy">
  &lt;sequence>
    &lt;element maxOccurs="1" minOccurs="0" name="originalSiteId" type="xsd:string"/>
    &lt;element maxOccurs="1" minOccurs="0" name="originalSiteName" type="xsd:string"/>
    &lt;element maxOccurs="1" minOccurs="1" name="newSiteName" nillable="false" type="xsd:string"/>
  &lt;/sequence>
&lt;/complexType></pre>
</description>
<example>$seed_site_id   = "a0d0fb818b7f08ee0990fe6e89648961";
$seed_site_name = "_rwd_seed";
$new_site_name  = "access-test";
$service->$seed_site_id   = "a0d0fb818b7f08ee0990fe6e89648961";
$seed_site_name = "_rwd_seed";
$new_site_name  = "access-test";
$service->siteCopy( $seed_site_id, $seed_site_name, $new_site_name );
</example>
<return-type>void</return-type></documentation>
*/
    function siteCopy( string $original_id, string $original_name, string $new_name ) 
    {
        $site_copy_params                   = new \stdClass();
        $site_copy_params->authentication   = $this->auth;
        $site_copy_params->originalSiteId   = $original_id;
        $site_copy_params->originalSiteName = $original_name;
        $site_copy_params->newSiteName      = $new_name;

        $this->reply = $this->soapClient->siteCopy( $site_copy_params );
        $this->storeResults( $this->reply->siteCopyReturn );
    }
/**
Unpublishes the asset with the given identifier.
@param stdClass $identifier The identifier of the object to be unpublished
@param Destination $destination The destination where the asset should be unpublished
<documentation><description><p>Unpublishes the asset with the given identifier.</p></description>
<example>$service->unpublish( $service->createId( a\Page::TYPE, $page_path, "cascade-admin" ) );</example>
<return-type>void</return-type></documentation>
*/
    public function unpublish( \stdClass $identifier, a\Destination $destination=NULL ) 
    {
        $publish_param = new \stdClass();
        $publish_info  = new \stdClass();
        $publish_param->authentication = $this->auth;
        $publish_info->identifier      = $identifier;
        
        if( isset( $destination ) )
        {
            if( is_array( $destination ) )
                $publish_info->destinations = $destination;
            else
                $publish_info->destinations = array( $destination );
        }
        
        $publish_info->unpublish           = true;
        $publish_param->publishInformation = $publish_info;
        
        $this->reply = $this->soapClient->publish( $publish_param );
        $this->storeResults( $this->reply->publishReturn );
    }
    
    // helper function
    private function storeResults( $return=NULL )
    {
        if( isset( $return ) )
        {
            $this->success  = $return->success;
            $this->message  = $return->message;
        }
        $this->lastRequest  = $this->soapClient->__getLastRequest();
        $this->lastResponse = $this->soapClient->__getLastResponse();
    }

    // from the constructor
    /*@var string The url */
    private $url;
    /*@var stdClass The authentication */
    private $auth;
    /*@var SoapClient The SoapClient */
    private $soapClient;
    
    // from the response
    /*@var string The message of the response */
    private $message;
    /*@var string The string 'true' or 'false' */
    private $success;
    /*@var string The id string of a created asset */
    private $createdAssetId;
    /*@var string The XML of the last request */
    private $lastRequest;
    /*@var string The XML of the last response */
    private $lastResponse;
    /*@var stdClass The object returned from an operation */
    private $reply;
    /*@var stdClass The audits object */
    private $audits;
    /*@var stdClass The searchMatches object */
    private $searchMatches;
    /*@var stdClass The listed messages */
    private $listed_messages;
    
    private $preferences;
    
    // 42 properties
    // property array to generate methods
    /*@var array The array of property names */
    private $properties = array(
        c\P::ASSETFACTORY,
        c\P::ASSETFACTORYCONTAINER,
        c\P::CONNECTORCONTAINER,
        c\P::CONTENTTYPE,
        c\P::CONTENTTYPECONTAINER,
        c\P::DATADEFINITION,
        c\P::DATADEFINITIONCONTAINER,
        c\P::DATABASETRANSPORT,
        c\P::DESTINATION,
        c\P::FACEBOOKCONNECTOR,
        c\P::FEEDBLOCK,
        c\P::FILE,
        c\P::FILESYSTEMTRANSPORT,
        c\P::FOLDER,
        c\P::FTPTRANSPORT,
        c\P::GOOGLEANALYTICSCONNECTOR,
        c\P::GROUP,
        c\P::INDEXBLOCK,
        c\P::METADATASET,
        c\P::METADATASETCONTAINER,
        c\P::PAGE,
        c\P::PAGECONFIGURATIONSET,
        c\P::PAGECONFIGURATIONSETCONTAINER,
        c\P::PUBLISHSET,
        c\P::PUBLISHSETCONTAINER,
        c\P::REFERENCE,
        c\P::ROLE,
        c\P::SCRIPTFORMAT,
        c\P::SITE,
        c\P::SITEDESTINATIONCONTAINER,
        c\P::SYMLINK,
        c\P::TARGET,
        c\P::TEMPLATE,
        c\P::TEXTBLOCK,
        c\P::TRANSPORTCONTAINER,
        c\P::USER,
        c\P::WORDPRESSCONNECTOR,
        c\P::WORKFLOWDEFINITION,
        c\P::WORKFLOWDEFINITIONCONTAINER,
        c\P::XHTMLDATADEFINITIONBLOCK,
        c\P::XMLBLOCK,
        c\P::XSLTFORMAT
    );
    
    // 46 types
    /*@var array The array of types of assets */
    private $types = array(
        c\T::ASSETFACTORY,
        c\T::ASSETFACTORYCONTAINER,
        c\T::CONNECTORCONTAINER,
        c\T::CONTENTTYPE,
        c\T::CONTENTTYPECONTAINER,
        c\T::DATADEFINITION,
        c\T::DATADEFINITIONCONTAINER,
        c\T::DESTINATION,
        c\T::FACEBOOKCONNECTOR,
        c\T::FEEDBLOCK,
        c\T::FILE,
        c\T::FOLDER,
        c\T::GOOGLEANALYTICSCONNECTOR,
        c\T::GROUP,
        c\T::INDEXBLOCK,
        c\T::MESSAGE,
        c\T::METADATASET,
        c\T::METADATASETCONTAINER,
        c\T::PAGE,
        c\T::PAGECONFIGURATION,
        c\T::PAGECONFIGURATIONSET,
        c\T::PAGECONFIGURATIONSETCONTAINER,
        c\T::PAGEREGION,
        c\T::PUBLISHSET,
        c\T::PUBLISHSETCONTAINER,
        c\T::REFERENCE,
        c\T::ROLE,
        c\T::SCRIPTFORMAT,
        c\T::SITE,
        c\T::SITEDESTINATIONCONTAINER,
        c\T::SYMLINK,
        c\T::TARGET,
        c\T::TEMPLATE,
        c\T::TEXTBLOCK,
        c\T::TRANSPORTDB,
        c\T::TRANSPORTFS,
        c\T::TRANSPORTFTP,
        c\T::TRANSPORTCONTAINER,
        c\T::USER,
        c\T::WORDPRESSCONNECTOR,
        c\T::WORKFLOW,
        c\T::WORKFLOWDEFINITION,
        c\T::WORKFLOWDEFINITIONCONTAINER,
        c\T::XHTMLDATADEFINITIONBLOCK,
        c\T::XMLBLOCK,
        c\T::XSLTFORMAT
    );
    
    /*@var array The array of readX names */
    private $read_methods = array();
    /*@var array The array of getX names */
    private $get_methods  = array();
    /*@var array The array to store property stdClass objects */
    private $read_assets  = array();
}
?>