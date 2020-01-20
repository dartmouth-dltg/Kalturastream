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
];