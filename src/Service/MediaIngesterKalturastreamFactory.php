<?php
namespace Kalturastream\Service;

use Kalturastream\Media\Ingester\Kalturastream;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class MediaIngesterKalturastreamFactory implements FactoryInterface
{
    /**
     * Create the Videostream media ingester service.
     *
     * @return Kalturastream
     */
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new Kalturastream(
            $services->get('Omeka\File\Downloader')
        );
    }
}
