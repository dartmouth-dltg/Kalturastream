<?php
namespace Kalturastream\Form;

use Laminas\Form\Form;
use Laminas\Validator\Callback;

class ConfigForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => 'text',
            'name' => 'kaltura_partner_id',
            'options' => [
                'label' => 'Partner ID', // @translate
                'info' => 'The Partner ID from Kaltura.', // @translate
            ],
            'attributes' => [
                'required' => true,
                'id' => 'partner_id',
            ],
        ]);

      $this->add([
        'type' => 'text',
        'name' => 'kaltura_uiconf_id',
        'options' => [
          'label' => 'KalturaUIconfID', // @translate
          'info' => 'The UI Configuration ID from Kaltura.', // @translate
        ],
        'attributes' => [
          'required' => true,
          'id' => 'uiconf_id',
        ],
      ]);

    }
}
