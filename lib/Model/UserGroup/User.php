<?php
/**
 * @copyright Copyright (c) 2021 René Gieling <github@dartcafe.de>
 *
 * @author René Gieling <github@dartcafe.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Polls\Model\UserGroup;

use DateTimeZone;
use OCA\Polls\Helper\Container;
use OCA\Polls\Model\Settings\AppSettings;
use OCP\IConfig;
use OCP\IUserManager;
use OCP\IUser;

class User extends UserBase {
	public const TYPE = 'user';
	public const ICON = 'icon-user';

	/** @var IConfig */
	private $config;

	/** @var AppSettings */
	protected $appSettings;

	/** @var IUser */
	private $user;

	public function __construct(
		string $id,
		string $type = self::TYPE
	) {
		parent::__construct($id, $type);
		$this->icon = self::ICON;
		$this->isNoUser = false;
		$this->description = Container::getL10N()->t('User');

		$this->config = Container::queryClass(IConfig::class);
		$this->user = Container::queryClass(IUserManager::class)->get($this->id);
		$this->displayName = $this->user->getDisplayName();
		$this->emailAddress = $this->user->getEmailAddress();
		$this->language = $this->config->getUserValue($this->id, 'core', 'lang');
		$this->locale = $this->config->getUserValue($this->id, 'core', 'locale');
		$this->appSettings = new AppSettings;
	}

	public function isEnabled(): bool {
		return $this->user->isEnabled();
	}

	public function getEmailAddressMasked(): string {
		if ($this->appSettings->getAllowSeeMailAddresses() && $this->emailAddress) {
			return $this->emailAddress;
		}
		return '';
	}

	public function getTimeZone(): DateTimeZone {
		$tz = $this->config->getUserValue($this->getId(), 'core', 'timezone');
		if ($tz) {
			return new DateTimeZone($tz);
		}
		return new DateTimeZone($this->timezone->getTimeZone()->getName());
	}

	public function getDescription(): string {
		if ($this->getEmailAddress()) {
			return $this->getEmailAddress();
		}
		return $this->description;
	}

	/**
	 * @return User[]
	 *
	 * @psalm-return list<User>
	 */
	public static function search(string $query = '', array $skip = []): array {
		$users = [];

		foreach (Container::queryClass(IUserManager::class)->search($query) as $user) {
			if (!in_array($user->getUID(), $skip)) {
				$users[] = new self($user->getUID());
			}
		}
		return $users;
	}
}
