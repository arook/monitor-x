<?php
/**
 * 
 * CLI Multi Process
 * @author kky979899
 *
 */
class Process {
	private $resource = array();
	private $pipes = array();

  public function get_alive_count() {
    $count = 0;
    foreach ($this->resource as $key=>$resource) {
      if (is_resource($resource)) {
        $status = proc_get_status($resource);
        if ($status['running'] == 1) {
          $count++;
        } else {
          unset($this->resource[$key]);
        }
      }
    }
    return $count;
  }

  public function add($command) {

    $descriptorspec = array(
			0	=>	array('pipe', 'r'),
			1	=>	array('pipe', 'w'),
			2	=>	array('pipe', 'w')
    );

    $this->resource[] = proc_open($command, $descriptorspec, $this->pipes[], null, $_ENV);

  }
	
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
