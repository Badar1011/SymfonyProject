<?php
/**
 * Created by PhpStorm.
 * User: stc661
 * Date: 10/12/18
 * Time: 14:35
 */

namespace Book\ReviewBundle\EventListener;


use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class RedirectAfterRegistrationSubscriber implements EventSubscriberInterface
{
    use TargetPathTrait;
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;

    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        $url = $this->getTargetPath($event->getRequest()->getSession(), 'main');

        if (!$url)
        {
            $url = $this->router->generate('book_index');
        }


        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
       return [
         FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess'
       ];
    }

}