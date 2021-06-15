<?php
namespace Kalturastream;

//use Videostream\Form\ConfigForm;
use Omeka\Module\AbstractModule;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\PhpRenderer;
use Kalturastream\Form\ConfigForm;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        $settings = $serviceLocator->get('Omeka\Settings');
        $settings->delete('Videostream_directory');
        $settings->delete('Videostream_delete_file');
    }
  public function getConfigForm(PhpRenderer $renderer)
  {
    $settings = $this->getServiceLocator()->get('Omeka\Settings');
    $form = new ConfigForm;
    $form->init();
    $form->setData([
      'kaltura_partner_id' => $settings->get('Kalturastream_partner_id'),
      'kaltura_uiconf_id' => $settings->get('Kalturastream_uiconf_id'),
    ]);
    return $renderer->formCollection($form, false);
  }
    public function handleConfigForm(AbstractController $controller)
    {
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $form = new ConfigForm;
        $form->init();
        $form->setData($controller->params()->fromPost());
        if (!$form->isValid()) {
            $controller->messenger()->addErrors($form->getMessages());
            return false;
        }
        $formData = $form->getData();
        $settings->set('Kalturastream_partner_id', $formData['kaltura_partner_id']);
        $settings->set('Kalturastream_uiconf_id', $formData['kaltura_uiconf_id']);
        return true;
    }
}
