<?php
namespace Acelaya\SlimContainerSm;

use Zend\ServiceManager\ServiceManager;

/**
 * Interface ServiceManagerAwareInterface
 * @author Alejandro Celaya Alastrué
 * @link http://www.alejandrocelaya.com
 */
interface ServiceManagerAwareInterface
{
    /**
     * @param ServiceManager $sm
     * @return mixed
     */
    public function setServiceManager(ServiceManager $sm);

    /**
     * @return ServiceManager
     */
    public function getServiceManager();
}
