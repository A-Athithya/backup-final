<?php
/**
 * NotificationService
 * 
 * Handles notification business logic
 */

class NotificationService {
    
    private $repo;
    
    public function __construct($db) {
        $this->repo = new NotificationRepository($db);
    }
    
    /**
     * Get notifications for a user
     */
    public function getNotifications($userId, $limit = 50, $offset = 0) {
        try {
            return $this->repo->getByUserId($userId, $limit, $offset);
        } catch (Exception $e) {
            Log::error('Error getting notifications: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get unread notifications count
     */
    public function getUnreadCount($userId) {
        try {
            return $this->repo->getUnreadCount($userId);
        } catch (Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId) {
        try {
            return $this->repo->markAsRead($notificationId, $userId);
        } catch (Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($userId) {
        try {
            return $this->repo->markAllAsRead($userId);
        } catch (Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Create a notification
     */
    public function create($data) {
        try {
            // Validate required fields
            if (empty($data['userId']) || empty($data['message'])) {
                throw new Exception('User ID and message are required');
            }
            
            return $this->repo->create($data);
        } catch (Exception $e) {
            Log::error('Error creating notification: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete a notification
     */
    public function delete($notificationId, $userId) {
        try {
            return $this->repo->delete($notificationId, $userId);
        } catch (Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            throw $e;
        }
    }
}
