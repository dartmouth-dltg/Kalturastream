<?php

namespace Kalturastream\Media\Ingester;

use Omeka\Api\Request;
use Omeka\Entity\Media;
use Omeka\File\Downloader;
use Omeka\Media\Ingester\IngesterInterface;
use Omeka\Settings\Settings;
use Omeka\Stdlib\ErrorStore;
use Zend\Form\Element\Text;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Renderer\PhpRenderer;

class Kalturastream implements IngesterInterface {
  /**
   * @var FileManager
   */
  protected $downloader;
  protected $settings;

  public function __construct(Downloader $downloader, Settings $settings) {
    $this->downloader = $downloader;
    $this->settings = $settings;
  }

  public function getLabel() {
    return 'KalturaStream'; // @translate
  }

  public function getRenderer() {
    return 'kalturastream';
  }

  public function ingest(Media $media, Request $request, ErrorStore $errorStore) {

    $data = $request->getContent();
    if (!isset($data['o:source'])) {
      $errorStore->addError('o:source', 'No Kaltura ID specified');
      return;
    }

    $partner_id = trim($request->getValue('partner_id'));
    if (is_numeric($partner_id)) {
      $mediaData['partner_id'] = $partner_id;
    }
    $uiconf = trim($request->getValue('uiconf'));
    if (is_numeric($uiconf)) {
      $mediaData['uiconf'] = $uiconf;
    }

    $start = trim($request->getValue('start'));
    if (is_numeric($start)) {
      $mediaData['start'] = $start;
    }
    $end = trim($request->getValue('end'));
    if (is_numeric($end)) {
      $mediaData['end'] = $end;
    }
    $width = trim($request->getValue('width'));
    if (is_numeric($width)) {
      $mediaData['width'] = $width;
    }
    $height = trim($request->getValue('height'));
    if (is_numeric($height)) {
      $mediaData['height'] = $height;
    }
    $mediaData['url'] = $data['o:source'];
    $media->setData($mediaData);
  }

  /**
   * {@inheritDoc}
   */
  public function form(PhpRenderer $view, array $options = []) {
    $partnerID = $this->settings->get('Kalturastream_partner_id');
    $uiconf = $this->settings->get('Kalturastream_uiconf_id');
    $idInput = new Text('o:media[__index__][o:source]');
    $idInput->setOptions([
      'label' => 'Video ID', // @translate
      'info' => 'ID for the video to embed, y\'all.', // @translate
    ]);
    $idInput->setAttributes([
      'id' => 'media-kalturastream-source-__index__',
      'required' => true,
    ]);

    $partnerInput = new Text('o:media[__index__][partner_id]');
    $partnerInput->setOptions([
      'label' => 'Partner ID', // @translate
      'info' => 'Kaltura Partner ID from Kaltura Mediaspace embed code',
      'value' => 't'// @translate
    ]);
    $partnerInput->setValue($partnerID);
    $partnerInput->setAttributes([
      'id' => 'media-kalturastream-source-__index__',
      'required' => true
    ]);
    $uiconfInput = new Text('o:media[__index__][uiconf]');
    $uiconfInput->setOptions([
      'label' => 'Kaltura UIconf ID', // @translate
      'info' => 'Kaltura UIconf ID from the Kaltura Mediaspace embed code', // @translate
    ]);
    $uiconfInput->setValue($uiconf);
    $uiconfInput->setAttributes([
      'id' => 'media-kalturastream-source-__index__',
      'required' => true
    ]);

    $startInput = new Text('o:media[__index__][start]');
    $startInput->setOptions([
      'label' => 'Start Time', // @translate
      'info' => 'Start time of video segment', // @translate
    ]);
    $startInput->setAttributes([
      'id' => 'media-kalturastream-source-__index__',
      'required' => false
    ]);
    $endInput = new Text('o:media[__index__][end]');
    $endInput->setOptions([
      'label' => 'End Time', // @translate
      'info' => 'End time of video segment', // @translate
    ]);
    $endInput->setAttributes([
      'id' => 'media-kalturastream-source-__index__',
      'required' => false
    ]);
    $widthInput = new Text('o:media[__index__][width]');
    $widthInput->setOptions([
      'label' => 'Width', // @translate
      'info' => 'Width of video display as recommended in Kaltura', // @translate
    ]);
    $heightInput = new Text('o:media[__index__][height]');
    $heightInput->setOptions([
      'label' => 'Height', // @translate
      'info' => 'Height of video display as recommended in Kaltura.', // @translate
    ]);
    return $view->formRow($idInput)
      . $view->formRow($partnerInput)
      . $view->formRow($uiconfInput)
      . $view->formRow($startInput)
      . $view->formRow($endInput)
      . $view->formRow($widthInput)
      . $view->formRow($heightInput);
  }
}
