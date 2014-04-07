<?php

namespace SS6\CoreBundle\Model\Security\Listener;

use DateTime;
use Doctrine\ORM\EntityManager;
use SS6\CoreBundle\Model\Security\UniqueLoginInterface;
use SS6\CoreBundle\Model\Security\TimelimitLoginInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener {
	
	/**
	 * @var EntityManager
	 */
	private $em;
	
	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}
	
	/**
	 * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
	 */
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) {
		$token = $event->getAuthenticationToken();
		$user = $token->getUser();
		
		if ($user instanceof TimelimitLoginInterface) {
			$user->setLastActivity(new DateTime());
		}
		
		if ($user instanceof UniqueLoginInterface) {
			$user->setLoginToken(uniqid());
			$this->em->persist($user);
			$this->em->flush();
		}
	}
}
