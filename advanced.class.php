<?php

class AdvancedPiPHP
{

	public function getPIDS()
	{
		return $this->execute('ps aux | less');
	}

	public function startTimelapse($interval, $duration, $prefix = 'timelapse', $quality = 100, $makeProcess = false)
	{
		$command = 'raspistill -o ' . $prefix . '%04d.jpg -t ' . $duration . ' -tl ' . $interval;
		if ($quality != 100) $command .= ' -q ' . $quality;
		if ($makeProcess) $command .= ' &';

		return $this->execute($command);
	}

	public function stopTimelapse($pid)
	{
		
	}

}