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

	public function changeDir($dirName)
	{
		return $this->execute('cd ' . $directoryName);
	}

	public function makeDir($dirName, $attributes = array())
	{
		return $this->execute('mkdir ' . $dirName . $this->parseAttr($attributes));
	}

	public function renameDir($dirName, $newDirName)
	{
		return $this->execute('mv ' . $dirName . ' ' . $newDirName);
	}

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

	public function dirExists($dirName)
	{
		$sshResponse = $this->execute('if [ ! -d ' . $dirName . ' ]; then fi');
	}

	public function dirHasContent()
	{
		$sshResponse = $this->execute('if [ "$(ls -A $DIR 2> /dev/null)" == "" ]; then echo "noContent" fi');

		if ($sshResponse == 'noContent')
		{
			return false;
		}

		return true;
	}

	public function getDirContents()
	{
		return $this->execute('ls -l');
	}

	public function getPID($process)
	{
		return $this->execute('pidof ' . $process);
	}

	/////////////////////////////////
	// VIDEO & PHOTO FUNCTIONALITY //
	/////////////////////////////////

	public function takePicture($filename)
	{
		return $this->execute('raspistill -o ' . $filename);
	}
}