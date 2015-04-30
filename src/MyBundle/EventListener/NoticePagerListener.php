<?php

namespace MyBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class NoticePagerListener
{
    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->getRequest()->get("_controller") === "mybundle.controller.job_controller:publishAction") {
            $session = $event->getRequest()->getSession();
            /** @var Session $session */
            $session->getFlashBag()->add('notice', 'Your job is now online for 30 days.');
        }

        if ($event->getRequest()->get("_controller") === "mybundle.controller.job_controller:extendAction") {
            $session = $event->getRequest()->getSession();
            /** @var Session $session */
            $session->getFlashBag()->add('notice', 'Your job is now extender by 30 days.');
        }
    }
}
