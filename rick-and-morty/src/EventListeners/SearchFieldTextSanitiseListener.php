<?php

namespace App\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class SearchFieldTextSanitiseListener
 * @package App\EventListeners
 */
class SearchFieldTextSanitiseListener implements EventSubscriberInterface
{
    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event) : void
    {
        $data = $event->getData();

        // replace any non word or space characters
        $event->setData(preg_replace('/[^\w\s]/','',$data));

    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents() : array
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit'
        ];
    }
}
