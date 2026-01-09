<?php
declare(strict_types=1);

class FreshRSS_UserActivityLogDAO extends Minz_ModelPdo {

	/**
	 * Log a user activity
	 * @param array{user_id:int,entry_id:string,action:string,timestamp?:int,ip_address?:?string,user_agent?:?string} $valuesTmp
	 */
	public function logActivity(array $valuesTmp): bool {
		$sql = <<<'SQL'
INSERT INTO `_user_activity_log` (user_id, entry_id, action, timestamp, ip_address, user_agent)
VALUES (:user_id, :entry_id, :action, :timestamp, :ip_address, :user_agent)
SQL;
		$stm = $this->pdo->prepare($sql);
		if ($stm !== false) {
			$stm->bindValue(':user_id', $valuesTmp['user_id'], PDO::PARAM_INT);
			$stm->bindValue(':entry_id', $valuesTmp['entry_id'], PDO::PARAM_STR);
			$stm->bindValue(':action', $valuesTmp['action'], PDO::PARAM_STR);
			$stm->bindValue(':timestamp', $valuesTmp['timestamp'] ?? time(), PDO::PARAM_INT);
			$stm->bindValue(':ip_address', $valuesTmp['ip_address'] ?? null, $valuesTmp['ip_address'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
			$stm->bindValue(':user_agent', $valuesTmp['user_agent'] ?? null, $valuesTmp['user_agent'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

			if ($stm->execute()) {
				return true;
			}
		}
		$info = $stm === false ? $this->pdo->errorInfo() : $stm->errorInfo();
		Minz_Log::error('SQL error ' . __METHOD__ . json_encode($info));
		return false;
	}

	/**
	 * Get activity logs for a user
	 * @param int $userId
	 * @param int $limit
	 * @param int $offset
	 * @return Traversable<FreshRSS_UserActivityLog>
	 */
	public function getUserActivityLogs(int $userId, int $limit = 100, int $offset = 0): Traversable {
		$sql = <<<'SQL'
SELECT id, user_id, entry_id, action, timestamp, ip_address, user_agent
FROM `_user_activity_log`
WHERE user_id = :user_id
ORDER BY timestamp DESC
LIMIT :limit OFFSET :offset
SQL;
		$stm = $this->pdo->prepare($sql);
		if ($stm !== false &&
			$stm->bindValue(':user_id', $userId, PDO::PARAM_INT) &&
			$stm->bindValue(':limit', $limit, PDO::PARAM_INT) &&
			$stm->bindValue(':offset', $offset, PDO::PARAM_INT) &&
			$stm->execute()) {
			while (is_array($row = $stm->fetch(PDO::FETCH_ASSOC))) {
				/** @var array{id:int,user_id:int,entry_id:string,action:string,timestamp:int,ip_address:?string,user_agent:?string} $row */
				yield FreshRSS_UserActivityLog::fromArray($row);
			}
		} else {
			$info = $stm === false ? $this->pdo->errorInfo() : $stm->errorInfo();
			Minz_Log::error('SQL error ' . __METHOD__ . json_encode($info));
		}
	}

	/**
	 * Get activity logs for an entry
	 * @param string $entryId
	 * @param int $limit
	 * @param int $offset
	 * @return Traversable<FreshRSS_UserActivityLog>
	 */
	public function getEntryActivityLogs(string $entryId, int $limit = 100, int $offset = 0): Traversable {
		$sql = <<<'SQL'
SELECT id, user_id, entry_id, action, timestamp, ip_address, user_agent
FROM `_user_activity_log`
WHERE entry_id = :entry_id
ORDER BY timestamp DESC
LIMIT :limit OFFSET :offset
SQL;
		$stm = $this->pdo->prepare($sql);
		if ($stm !== false &&
			$stm->bindValue(':entry_id', $entryId, PDO::PARAM_STR) &&
			$stm->bindValue(':limit', $limit, PDO::PARAM_INT) &&
			$stm->bindValue(':offset', $offset, PDO::PARAM_INT) &&
			$stm->execute()) {
			while (is_array($row = $stm->fetch(PDO::FETCH_ASSOC))) {
				/** @var array{id:int,user_id:int,entry_id:string,action:string,timestamp:int,ip_address:?string,user_agent:?string} $row */
				yield FreshRSS_UserActivityLog::fromArray($row);
			}
		} else {
			$info = $stm === false ? $this->pdo->errorInfo() : $stm->errorInfo();
			Minz_Log::error('SQL error ' . __METHOD__ . json_encode($info));
		}
	}

	/**
	 * Clean old activity logs
	 * @param int $daysOld Number of days to keep logs
	 * @return int|false Number of deleted records or false on error
	 */
	public function cleanOldLogs(int $daysOld = 90): int|false {
		$timestamp = time() - ($daysOld * 86400);
		$sql = 'DELETE FROM `_user_activity_log` WHERE timestamp < :timestamp';
		$stm = $this->pdo->prepare($sql);
		if ($stm !== false &&
			$stm->bindValue(':timestamp', $timestamp, PDO::PARAM_INT) &&
			$stm->execute()) {
			return $stm->rowCount();
		}
		$info = $stm === false ? $this->pdo->errorInfo() : $stm->errorInfo();
		Minz_Log::error('SQL error ' . __METHOD__ . json_encode($info));
		return false;
	}
}
