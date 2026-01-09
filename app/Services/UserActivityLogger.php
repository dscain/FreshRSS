<?php
declare(strict_types=1);

/**
 * Service for logging user activities on entries.
 */
class FreshRSS_UserActivityLogger {

	private readonly FreshRSS_UserActivityLogDAO $activityLogDAO;

	public function __construct() {
		$this->activityLogDAO = FreshRSS_Factory::createUserActivityLogDao();
	}

	/**
	 * Log when a user marks an entry as read or unread
	 */
	public function logReadAction(string $entryId, bool $isRead): void {
		$this->logActivity($entryId, $isRead ? 'read' : 'unread');
	}

	/**
	 * Log when a user bookmarks (stars) an entry
	 */
	public function logBookmarkAction(string $entryId, bool $isBookmarked): void {
		$this->logActivity($entryId, $isBookmarked ? 'star' : 'unstar');
	}

	/**
	 * Log when a user clicks on an external link
	 */
	public function logExternalLinkClick(string $entryId): void {
		$this->logActivity($entryId, 'external_link_click');
	}

	/**
	 * Log when a user views an entry's content/summary
	 */
	public function logContentView(string $entryId): void {
		$this->logActivity($entryId, 'content_view');
	}

	/**
	 * Generic method to log any activity
	 */
	public function logActivity(string $entryId, string $action, ?int $timestamp = null, ?string $ipAddress = null, ?string $userAgent = null): void {
		$userId = FreshRSS_Context::currentUser() ? FreshRSS_Context::currentUser()->id() : null;
		if ($userId === null) {
			return; // Don't log for anonymous users
		}

		$activityData = [
			'user_id' => $userId,
			'entry_id' => $entryId,
			'action' => $action,
		];

		if ($timestamp !== null) {
			$activityData['timestamp'] = $timestamp;
		}

		if ($ipAddress !== null) {
			$activityData['ip_address'] = $ipAddress;
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$activityData['ip_address'] = $_SERVER['REMOTE_ADDR'];
		}

		if ($userAgent !== null) {
			$activityData['user_agent'] = $userAgent;
		} elseif (isset($_SERVER['HTTP_USER_AGENT'])) {
			$activityData['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		}

		$this->activityLogDAO->logActivity($activityData);
	}

	/**
	 * Get activity logs for current user
	 */
	public function getCurrentUserActivityLogs(int $limit = 100, int $offset = 0): Traversable {
		$userId = FreshRSS_Context::currentUser() ? FreshRSS_Context::currentUser()->id() : null;
		if ($userId === null) {
			return new ArrayIterator([]);
		}

		return $this->activityLogDAO->getUserActivityLogs($userId, $limit, $offset);
	}

	/**
	 * Get activity logs for an entry
	 */
	public function getEntryActivityLogs(string $entryId, int $limit = 100, int $offset = 0): Traversable {
		return $this->activityLogDAO->getEntryActivityLogs($entryId, $limit, $offset);
	}

	/**
	 * Clean old activity logs
	 */
	public function cleanOldLogs(int $daysOld = 90): int|false {
		return $this->activityLogDAO->cleanOldLogs($daysOld);
	}
}
