<?php
/**
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 *
 * @author Bernhard Posselt <dev@bernhard-posselt.com>
 * @author Christoph Wurst <christoph@owncloud.com>
 * @author Joas Schilling <coding@schilljs.com>
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 * @author Victor Dubiniuk <dubiniuk@owncloud.com>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\Core;

use OC\AppFramework\Utility\SimpleContainer;
use OC\AppFramework\Utility\TimeFactory;
use OC\Core\Controller\LoginController;
use OC\Core\Controller\LostController;
use OC\Core\Controller\TokenController;
use OC\Core\Controller\TwoFactorChallengeController;
use OC\Core\Controller\UserController;
use OCP\AppFramework\App;
use OCP\Util;

/**
 * Class Application
 *
 * @package OC\Core
 */
class Application extends App {

	/**
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams=array()){
		parent::__construct('core', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('LostController', function(SimpleContainer $c) {
			return new LostController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('URLGenerator'),
				$c->query('UserManager'),
				$c->query('Defaults'),
				$c->query('L10N'),
				$c->query('Config'),
				$c->query('SecureRandom'),
				$c->query('DefaultEmailAddress'),
				$c->query('IsEncryptionEnabled'),
				$c->query('Mailer'),
				$c->query('TimeFactory')
			);
		});
		$container->registerService('LoginController', function(SimpleContainer $c) {
			return new LoginController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('UserManager'),
				$c->query('Config'),
				$c->query('Session'),
				$c->query('UserSession'),
				$c->query('URLGenerator'),
				$c->query('TwoFactorAuthManager'),
				$c->query('ServerContainer')->getBruteforceThrottler()
			);
		});
		$container->registerService('TwoFactorChallengeController', function (SimpleContainer $c) {
			return new TwoFactorChallengeController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('TwoFactorAuthManager'),
				$c->query('UserSession'),
				$c->query('Session'),
				$c->query('URLGenerator'));
		});
		$container->registerService('TokenController', function(SimpleContainer $c) {
			return new TokenController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('UserManager'),
				$c->query('ServerContainer')->query('OC\Authentication\Token\IProvider'),
				$c->query('TwoFactorAuthManager'),
				$c->query('SecureRandom')
			);
		});

		/**
		 * Core class wrappers
		 */
		$container->registerService('IsEncryptionEnabled', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getEncryptionManager()->isEnabled();
		});
		$container->registerService('URLGenerator', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getURLGenerator();
		});
		$container->registerService('UserManager', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getUserManager();
		});
		$container->registerService('Config', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getConfig();
		});
		$container->registerService('L10N', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getL10N('core');
		});
		$container->registerService('SecureRandom', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getSecureRandom();
		});
		$container->registerService('Session', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getSession();
		});
		$container->registerService('UserSession', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getUserSession();
		});
		$container->registerService('Defaults', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getThemingDefaults();
		});
		$container->registerService('Mailer', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getMailer();
		});
		$container->registerService('TimeFactory', function(SimpleContainer $c) {
			return new TimeFactory();
		});
		$container->registerService('DefaultEmailAddress', function() {
			return Util::getDefaultEmailAddress('lostpassword-noreply');
		});
		$container->registerService('TwoFactorAuthManager', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getTwoFactorAuthManager();
		});
	}

}
