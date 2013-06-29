<?php
namespace OpenSkedge\AppBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Changes Form->bind() behavior so that it treats not set values as if they
 * were sent unchanged.
 *
 * Use when you don't want fields to be set to NULL when they are not displayed
 * on the page (or to implement PUT/PATCH requests).
 *
 * Taken from https://gist.github.com/makasim/3720535
 *
 * @category EventSubscriber
 * @package  OpenSkedge\AppBundle\Form\EventSubscriber
 * @author   A.S.Kozienko <a.s.kozienko@gmail.com>, Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class PatchSubscriber implements EventSubscriberInterface
{
    public function onPreBind(FormEvent $event)
    {
        $form = $event->getForm();
        $clientData = $event->getData();
        $clientData = array_replace($this->unbind($form), $clientData ?: array());
        $event->setData($clientData);
    }

    /**
     * Returns the form's data like $form->bind() expects it
     */
    protected function unbind($form)
    {
        if ($form->count() > 0) {
            $ary = array();
            foreach ($form->all() as $name => $child) {
                $ary[$name] = $this->unbind($child);
            }
            return $ary;
        } else {
            return $form->getViewData();
        }
    }

    static public function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_BIND => 'onPreBind',
        );
    }
}
