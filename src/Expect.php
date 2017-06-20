<?php


/* 

include_once 'Expect.php';


$expect=new Expect('php -v');
//$expect->send();

$expectations=array('*PHP*','ho');
$responded=$expect->expect($expectations);

if($responded!=-1){
	$response=$expectations[$responded];
	
	echo "$response (".$expect->lastLoggedResponse.')';
}
else{
	echo "didn't respond with expected answer";
}
$expect->closeProcess();


*/
class Expect{
	const DEFAULT_TIMEOUT=9999999;
	private $cmd;
	private $cwd;
	private $pipes;
	private $process;
	private $logger;
	public $lastLoggedResponse=null;

	public function __construct($cmd,$cwd=null,LoggerInterface $logger=null){
		$this->cmd=$cmd;
		$this->cwd=$cwd;
		$this->logger=$logger ?: new NullLogger();
		$this->createProcess();
	}
	public function expect($output,$timeout=self::DEFAULT_TIMEOUT){
		return $this->waitForExpectedResponse($output,$timeout);
	}
	public function send($input){
		if(stripos(strrev($input),PHP_EOL) === false){
			$input=$input . PHP_EOL;
		}
		return $this->sendInput($input);
	}
	private function createProcess(){
		$this->process=proc_open($this->cmd,array(array('pipe','r'),array('pipe','w'),array('pipe','r')),$this->pipes,$this->cwd);
		
		if(!is_resource($this->process)){
			throw new \RuntimeException('Could not create the process.');
		}
	}
	public function closeProcess(){
		fclose($this->pipes[0]);
		fclose($this->pipes[1]);
		fclose($this->pipes[2]);
		proc_close($this->process);
	}
	private function waitForExpectedResponse($expectations,$timeout){
		if(!is_array($expectations)){
			$expectations=array($expectations);
		}
		$response=null;
		$buffer='';
		$start=time();
		stream_set_blocking($this->pipes[1],false);
		
		while(true){
			if(time()-$start>=$timeout){
				throw new ProcessTimeoutException();
			}
			if(feof($this->pipes[1])){
				throw new UnexpectedEOFException();
			}
			if(!$this->isRunning()){
				throw new ProcessTerminatedException();
			}
			$buffer.=fread($this->pipes[1],4096);
			$response=static::trimAnswer($buffer);
			
			if($response !== '' && $response !== $this->lastLoggedResponse){
				$this->lastLoggedResponse=$response;
				$this->logger->info("Expected '".print_r($expectations,true)."',got '{$response}'");
			}
			
			$expectationnum=0;
			
			foreach($expectations as $expectation){
				if(fnmatch($expectation,$response)){
					return $expectationnum;
				}
				$expectationnum++;
			}
		}
		return -1;
	}
	private function sendInput($input){
		$this->logger->info("Sending '{$input}'");
		fwrite($this->pipes[0],$input);
	}
	private static function trimAnswer($str){
		return preg_replace('{\r?\n$}D','',$str);
	}
	private function isRunning(){
		if(!is_resource($this->process)){
			return false;
		}
		$status=proc_get_status($this->process);
		return $status['running'];
	}
}

class FailedExpectationException extends \RuntimeException{}
class ProcessTerminatedException extends FailedExpectationException{}
class ProcessTimeoutException extends FailedExpectationException{}
class UnexpectedEOFException extends FailedExpectationException{}
class NullLogger{function info($data=''){}}
