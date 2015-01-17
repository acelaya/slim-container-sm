<?php
namespace Acelaya\SlimContainerSm;

use Slim\Helper\Set;

interface SlimContainerConsumibleInterface
{
    /**
     * Makes this to consume the services defined in provided container
     *
     * @param Set $container
     * @return mixed
     */
    public function consumeSlimContainer(Set $container);
}
