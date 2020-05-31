<?php

namespace Kalturastream;

return [
  'media_ingesters' => [
    'factories' => [
      'kalturastream' => Service\MediaIngesterKalturastreamFactory::class,
    ],
  ],
  'media_renderers' => [
    'invokables' => [
      'kalturastream' => Media\Renderer\Kalturastream::class,
    ],
  ],
  'kalturastream' => [
    'config' => [
      'kaltura_partner_id' => '',
      'kaltura_uiconf_id' => '',

    ],
  ],
];
