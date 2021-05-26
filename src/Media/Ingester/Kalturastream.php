<?php

namespace Kalturastream\Media\Ingester;

use Omeka\Api\Representation\MediaRepresentation;
use Omeka\Api\Request;
use Omeka\Entity\Media;
use Omeka\File\Downloader;
use Omeka\Media\Ingester\MutableIngesterInterface;
use Omeka\Settings\Settings;
use Omeka\Stdlib\ErrorStore;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;
use Laminas\View\Renderer\PhpRenderer;

class Kalturastream implements MutableIngesterInterface {

  protected $downloader;
  protected $settings;

  public function __construct(Downloader $downloader, Settings $settings) {
    $this->downloader = $downloader;
    $this->settings = $settings;
  }

  public function update(Media $media, Request $request, ErrorStore $errorStore) {
    $data = $request->getContent();
    $values = \array_filter($data['o:media']['__index__']);
    $media->setData($values);
  }

  public function updateForm(PhpRenderer $view, MediaRepresentation $media, array $options = []) {
    $data = $media->mediaData();
    return $this->form($view, $data);
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
    $mediaData['o:source'] = $data['o:source'];
    $media->setData($mediaData);
  }

  /**
   * {@inheritDoc}
   */
  public function form(PhpRenderer $view, array $options = []) {

    $partnerID = isset($options['partner_id']) ? $options['partner_id'] : $this->settings->get('Kalturastream_partner_id');
    $uiconf = isset($options['uiconf']) ? $options['uiconf'] : $this->settings->get('Kalturastream_uiconf_id');
    $mime = isset($options['mimr']) ? $options['mime'] : 'application/octet-stream';

    $idInput = new Text('o:media[__index__][o:source]');
    $idInput->setOptions([
      'label' => 'Video ID', // @translate
      'info' => 'ID for the video to embed.', // @translate
    ]);
    $idInput->setValue($options['o:source']);
    $idInput->setAttributes([
      'id' => 'media-kalturastream-source-__index__',
      'required' => true,
    ]);

    $mimeInput = new Text('o:media[__index__][mime]');
    $mimeInput->setOptions([
      'label' => 'Mime Type', // @translate
      'info' => 'mimetype of video segment', // @translate
    ]);
    $mimeInput->setAttributes([
      'id' => 'media-kalturastream-source-__index__',
      'required' => false
    ]);
    $mimeInput->setValue($mime);

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
    if (isset($options['start'])) {
      $startInput->setValue($options['start']);
    }

    $endInput = new Text('o:media[__index__][end]');
    $endInput->setOptions([
      'label' => 'End Time', // @translate
      'info' => 'End time of video segment', // @translate
    ]);
    $endInput->setAttributes([
      'id' => 'media-kalturastream-source-__index__',
      'required' => false
    ]);
    if (isset($options['end'])) {
      $endInput->setValue($options['end']);
    }

    $widthInput = new Text('o:media[__index__][width]');
    $widthInput->setOptions([
      'label' => 'Width', // @translate
      'info' => 'Width of video display as recommended in Kaltura', // @translate
    ]);
    if (isset($options['width'])) {
      $widthInput->setValue(($options['width']));
    }

    $heightInput = new Text('o:media[__index__][height]');
    $heightInput->setOptions([
      'label' => 'Height', // @translate
      'info' => 'Height of video display as recommended in Kaltura.', // @translate
    ]);
    if (isset($options['width'])) {
      $heightInput->setValue($options['width']);
    }

    $autoplayInput = new Checkbox('o:media[__index__][autoplay]');
    $autoplayInput->setOptions([
      'label' => 'Autoplay', // @translate
      'info' => 'Begin Video play on load?',
      'checked_value' => 'autoplay',
      'unchecked_value' => ''// @translate
    ]);
    if (isset($options['autoplay'])) {
      $autoplayInput->setValue($options['autoplay']);
    }
    return $view->formRow($idInput)
      . $view->formRow($mimeInput)
      . $view->formRow($partnerInput)
      . $view->formRow($uiconfInput)
      . $view->formRow($startInput)
      . $view->formRow($endInput)
      . $view->formRow($widthInput)
      . $view->formRow($heightInput)
      . $view->formRow($autoplayInput);
  }
}
