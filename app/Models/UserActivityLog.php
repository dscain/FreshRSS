<?php
declare(strict_types=1);

class FreshRSS_UserActivityLog extends Minz_Model {

	private int $id = 0;
	private int $userId;
	private string $entryId;
	private string $action;
	private int $timestamp;
	private ?string $ipAddress;
	private ?string $userAgent;

	public function id(): int {
		return $this->id;
	}

	public function userId(): int {
		return $this->userId;
	}

	public function entryId(): string {
		return $this->entryId;
	}

	public function action(): string {
		return $this->action;
	}

	public function timestamp(): int {
		return $this->timestamp;
	}

	public function ipAddress(): ?string {
		return $this->ipAddress;
	}

	public function userAgent(): ?string {
		return $this->userAgent;
	}

	public function _id(int $id): void {
		$this->id = $id;
	}

	public function _userId(int $userId): void {
		$this->userId = $userId;
	}

	public function _entryId(string $entryId): void {
		$this->entryId = $entryId;
	}

	public function _action(string $action): void {
		$this->action = $action;
	}

	public function _timestamp(int $timestamp): void {
		$this->timestamp = $timestamp;
	}

	public function _ipAddress(?string $ipAddress): void {
		$this->ipAddress = $ipAddress;
	}

	public function _userAgent(?string $userAgent): void {
		$this->userAgent = $userAgent;
	}

	/** @param array{id?:int,user_id?:int,entry_id?:string,action?:string,timestamp?:int,ip_address?:?string,user_agent?:?string} $dao */
	public static function fromArray(array $dao): FreshRSS_UserActivityLog {
		$log = new FreshRSS_UserActivityLog();
		if (isset($dao['id'])) {
			$log->_id((int)$dao['id']);
		}
		if (isset($dao['user_id'])) {
			$log->_userId((int)$dao['user_id']);
		}
		if (isset($dao['entry_id'])) {
			$log->_entryId((string)$dao['entry_id']);
		}
		if (isset($dao['action'])) {
			$log->_action($dao['action']);
		}
		if (isset($dao['timestamp'])) {
			$log->_timestamp((int)$dao['timestamp']);
		}
		if (isset($dao['ip_address'])) {
			$log->_ipAddress($dao['ip_address']);
		}
		if (isset($dao['user_agent'])) {
			$log->_userAgent($dao['user_agent']);
		}
		return $log;
	}

	/** @return array{id:int,user_id:int,entry_id:string,action:string,timestamp:int,ip_address:?string,user_agent:?string} */
	public function toArray(): array {
		return [
			'id' => $this->id(),
			'user_id' => $this->userId(),
			'entry_id' => $this->entryId(),
			'action' => $this->action(),
			'timestamp' => $this->timestamp(),
			'ip_address' => $this->ipAddress(),
			'user_agent' => $this->userAgent(),
		];
	}
}
