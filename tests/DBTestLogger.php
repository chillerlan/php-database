<?php
/**
 * Class OAuthTestLogger
 *
 * @created      04.05.2019
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2019 smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest;

use Psr\Log\{AbstractLogger, LogLevel};
use Exception;

use function array_key_exists, date, sprintf, str_repeat, str_replace, strtolower, substr, trim, var_export;

final class DBTestLogger extends AbstractLogger{

	protected const E_NONE      = 0x00;
	protected const E_DEBUG     = 0x01;
	protected const E_INFO      = 0x02;
	protected const E_NOTICE    = 0x04;
	protected const E_WARNING   = 0x08;
	protected const E_ERROR     = 0x10;
	protected const E_CRITICAL  = 0x20;
	protected const E_ALERT     = 0x40;
	protected const E_EMERGENCY = 0x80;

	protected const LEVELS = [
		'none'              => self::E_NONE,
		LogLevel::DEBUG     => self::E_DEBUG,
		LogLevel::INFO      => self::E_INFO,
		LogLevel::NOTICE    => self::E_NOTICE,
		LogLevel::WARNING   => self::E_WARNING,
		LogLevel::ERROR     => self::E_ERROR,
		LogLevel::CRITICAL  => self::E_CRITICAL,
		LogLevel::ALERT     => self::E_ALERT,
		LogLevel::EMERGENCY => self::E_EMERGENCY,
	];

	/**
	 * @see \Psr\Log\LogLevel
	 */
	protected string $loglevel;

	/**
	 * OAuthTestLogger constructor.
	 *
	 * @param string|null $loglevel
	 */
	public function __construct(string $loglevel = null){
		$this->setLoglevel($loglevel ?? 'none');
	}

	/**
	 * @param string $loglevel
	 *
	 * @throws \Exception
	 */
	public function setLoglevel(string $loglevel):void{
		$loglevel = strtolower($loglevel);

		if(!array_key_exists($loglevel, $this::LEVELS)){
			throw new Exception('invalid loglevel');
		}

		$this->loglevel = $loglevel;
	}

	/**
	 * @inheritDoc
	 */
	public function log($level, $message, array $context = []):void{

		if($this->loglevel === 'none' || !isset($this::LEVELS[$level]) || !isset($this::LEVELS[$this->loglevel])){
			return;
		}

		if($this::LEVELS[$level] >= $this::LEVELS[$this->loglevel]){
			echo sprintf(
				'[%s][%s] %s',
				date('Y-m-d H:i:s'),
				substr($level, 0, 4),
				str_replace("\n", "\n".str_repeat(' ', 28), trim($message))
			)."\n";

			if(!empty($context)){
				$c = "\n--- CONTEXT START ---\n";

				foreach($context as $k => $v){
					$c .= '\''.$k.'\' => '.var_export($v, true)."\n";
				}

				$c .= "--- CONTEXT END ---\n\n";

				echo $c;
			}
		}

	}

}
