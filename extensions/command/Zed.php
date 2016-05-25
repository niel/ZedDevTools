<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program (see LICENSE.txt in the base directory.  If
 * not, see:
 *
 * @link      <http://www.gnu.org/licenses/>.
 * @author    niel
 * @copyright 2016 nZEDb
 */
namespace li3_zeddev\extensions\command;

use \app\extensions\util\Git;
use \nzedb\db\DbUpdate;

/**
 * Tools for working on nZEDb.
 *
 * Actions:
 *  * commit	(replacement for the ./commit script).
 *
 * @package li3_zeddev\extensions\command
 */
class Zed extends \app\extensions\console\Command
{
	/**
	 * @var \app\extensions\util\Git object.
	 */
	protected $git;

	public function __construct(array $config = [])
	{
		parent::__construct($config);
	}

	public function commit()
	{
		system('git add -i > /dev/tty < /dev/tty', $status);
		if ($status !== 0) {
			//TODO handle the error
			exit($status);
		}

		system('nano Changelog > /dev/tty < /dev/tty', $status);
		if ($status !== 0) {
			//TODO handle the error
			exit($status);
		}

		system('git add Changelog', $status);
		if ($status !== 0) {
			//TODO handle the error
			exit($status);
		}

		$this->initialiseGit();

		if (in_array($this->git->getBranch(), $this->git->getBranchesMain())) {
			// TODO rewrite to use internal code not nzedb's.
			// Only update patches, etc. on specific branches to lessen conflicts
			try {
				// Run DbUpdates to make sure we're up to date.
				$DbUpdater = new DbUpdate(['git' => $git]);
				$DbUpdater->newPatches(['safe' => false]);
			} catch (\Exception $e) {
				$error = 1;
				echo "Error while checking patches!\n";
				echo $e->getMessage() . "\n";
			}
		}

		system('git commit > /dev/tty', $status);
	}

	protected function initialiseGit()
	{
		if (!($this->git instanceof Git)) {
			$this->git = new Git();
		}
	}
}
