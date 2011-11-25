<?php
/**
 * 
 * CLI Multi Process
 * @author kky979899
 *
 */
class Process {
	private $resource;
	private $pipes;
	
	public function open($command) {
		$descriptorspec = array(
			0	=>	array('pipe', 'r'),
			1	=>	array('pipe', 'w'),
			2	=>	array('pipe', 'w')
		);
		
		$this->resource = proc_open($command, $descriptorspec, $this->pipes, null, $_ENV);
	}
	
	public function read() {
		if (is_resource($this->resource)) {
			$retval = '';
			$error = '';
			
			$stdin = $this->pipes[0];
			$stdout = $this->pipes[1];
			$stderr = $this->pipes[2];
			
			while (! feof($stdout)) {
				$retval .= fgets($stdout);
			}
			
			while (! feof($stderr)) {
				$error .= fgets($stderr);
			}
			
			fclose($stdin);
			fclose($stdout);
			fclose($stderr);
			
			$exit_code = proc_close($this->resource);
		}
		
		if (! empty($error)) {
			throw new Exception($error);
		} else {
			return $retval;
		}
	}
}

?>