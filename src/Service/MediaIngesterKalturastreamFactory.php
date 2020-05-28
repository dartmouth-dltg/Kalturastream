<?php

namespace Kalturastream\Service;

use Interop\Container\ContainerInterface;
use Kalturastream\Media\Ingester\Kalturastream;
use Zend\ServiceManager\Factory\FactoryInterface;

class MediaIngesterKalturastreamFactory implements FactoryInterface {
  /**
   * Create the Videostream media ingester service.
   *
   * @return Kalturastream
   */
  public function __invoke(ContainerInterface $services, $requestedName, array $options = null) {
    return new Kalturastream(
      $services->get('Omeka\File\Downloader'),
      $services->get('Omeka\Settings')
    );
  }
}
