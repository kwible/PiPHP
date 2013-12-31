<?php

class PiPHP extends AdvancedPiPHP
{

	protected $handler;

	protected $hasSuperuserPerms = false;

	/**
	 * Opens a connection to the Raspberry Pi
	 * 
	 * @param string Host to connect to.
	 * @param string Specified username to start a session
	 * @param string Specified password to start a session
	 * @param int Portnumber where SSH connections are accepted
	 * @param bool Set to true to make the giver user automatically superuser
	 * 
	 * @return void
	 */

	public function open($host, $user, $password, $port = 22, $superuser = false)
	{
		$this->handler = new Net_SSH2($host);

		if (!$this->handler->login($user, $password))
		{
			throw new \Exception("Cannot connect to the Pi, because login details are incorrect");
		}
	}

	/**
	 * Executes a command
	 * 
	 * @param string Command who have to been executed
	 */

	public function execute($command)
	{
		return $this->handler->exec($command);
	}

	/**
	 * Parses the attributes 'string' for the command
	 *
	 * @param  $attributes 	All attributes
	 */

	public function parseAttr(array $attributes = array())
	{
		$string = ' ';

		foreach ($attributes as $key => $value)
		{
			$string .= $key . ' ' . $value;
		}

		return $string;
	}

	/**
	 * Return the hasSuperuserPerms parameter
	 * 
	 * @return bool
	 */

	public function isSU()
	{
		return $this->hasSuperuserPerms;
	}

	/**
	 * Makes the current session a superuser session
	 * 
	 * @return void
	 */

	public function makeSU()
	{
		$this->execute('sudo -i');
	}
	
	///////////////////////////
	// GENERAL FUNCTIONALITY //
	///////////////////////////

	/**
	 * Reboot the Raspberry Pi
	 * 
	 * @return void
	 */

	public function reboot()
	{
		if ($this->isSU()) return $this->execute('reboot'); return $this->execute('sudo reboot');
	}

	/**
	 * Shutdown the Raspberry Pi
	 */

	public function shutdown()
	{
		return $this->execute('sudo halt');
	}

	/**
	 * Kill a process
	 *
	 * @param int $pid The PID you want to kill
	 * @param bool  $su Run as superuser
	 *
	 * @return void
	 */

	public function kill($pid, $su = true)
	{
		($su == true) ? return $this->execute('sudo kill ' . $pid) : $this->execute('kill ' . $pid);
	}

	/**
	 * Killall processes assigned to a specified PID
	 *
	 * @param int $pid The PID you want to kill
	 * @param bool $su Run as superuser
	 */

	public function killall($pid, $su = true)
	{
		($su == true) ? return $this->execute('sudo killall ' . $pid) : $this->execute('killall ' . $pid);
	}

	/**
	 * Creates a file
	 * 
	 * @param  string $fileName The filename
	 * @return void
	 */
	
	public function createFile($fileName)
	{
		return $this->execute('touch ' . $fileName);
	}

	/**
	 * Change directory
	 *
	 * @param  string $dirName The directory name
	 * @return  void
	 */

	public function changeDir($dirName)
	{
		return $this->execute('cd ' . $directoryName);
	}

	/**
	 * Makes an directory
	 *
	 * @param  string $dirName The directory name
	 * @param  array $attributes All attributes
	 * @return  void 
	 */

	public function makeDir($dirName, $attributes = array())
	{
		return $this->execute('mkdir ' . $dirName . $this->parseAttr($attributes));
	}

	/**
	 * Renames an directory
	 *
	 * @param  string $dirName The directory you want to been renamed
	 * @param  string $replacement The name for the 'replacement' directory
	 * @return  void
	 */

	public function renameDir($dirName, $replacement)
	{
		return $this->execute('mv ' . $dirName . ' ' . $replacement);
	}

	/**
	 * Removes a directory
	 *
	 * @param  string $dirName The directory name
	 * @param  bool $withContents Remove a directory with all of his contents
	 * @return  void
	 */

	public function removeDir($dirName, $withContents = false)
	{
		if ($withContents)
		{
			return $this->console('rm -rf ' . $dirName);
		} else {
			if ($this->dirHasContent($dirName))
			{
				throw new \Exception("Failed to remove " . $dirName . ", because the directory has some content in it.");
			}

			$this->execute('rmdir ' . $dirName);
		}
	}

	/**
	 * Checks if a specified directory exists
	 *
	 * @param string $dirName The directory name
	 * @return void
	 */

	public function dirExists($dirName)
	{
		$sshResponse = $this->execute('if [ ! -d ' . $dirName . ' ]; then fi');
	}

	/**
	 * Checks if a specified directory has content
	 *
	 * 
	 */

	public function dirHasContent()
	{
		$sshResponse = $this->execute('if [ "$(ls -A $DIR 2> /dev/null)" == "" ]; then echo "noContent" fi');

		if ($sshResponse == 'noContent')
		{
			return false;
		}

		return true;
	}

	/**
	 * List the directory contents
	 *
	 * @return mixed
	 */

	public function getDirContents()
	{
		return $this->execute('ls -l');
	}

	/**
	 * Gets a PID of a specified process
	 *
	 * @param int $process The process name
	 */

	public function getPID($process)
	{
		return $this->execute('pidof ' . $process);
	}

	/////////////////////////////////
	// VIDEO & PHOTO FUNCTIONALITY //
	/////////////////////////////////
	
	/**
	 * Takes a picture with the Raspberry Pi camers
	 *
	 * @param  string $filename The filename
	 * @return void
	 */

	public function takePicture($filename)
	{
		return $this->execute('raspistill -o ' . $filename);
	}
}
