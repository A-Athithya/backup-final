<?php
/**
 * NotificationRepository
 * 
 * Handles database operations for notifications
 */

class NotificationRepository extends BaseRepository {
    
    /**
     * Get notifications by user ID
     */
    public function getByUserId($userId, $limit = 50, $offset = 0) {
        try {
            $query = "SELECT * FROM notifications 
                      WHERE user_id = ? AND deleted_at IS NULL 
                      ORDER BY created_at DESC 
                      LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('iii', $userId, $limit, $offset);
            $stmt->execute();
            
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
            
        } catch (Exception $e) {
            Log::error('Error in getByUserId: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get unread notifications count
     */
    public function getUnreadCount($userId) {
        try {
            $query = "SELECT COUNT(*) as count FROM notifications 
                      WHERE user_id = ? AND is_read = 0 AND deleted_at IS NULL";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row['count'] ?? 0;
            
        } catch (Exception $e) {
            Log::error('Error in getUnreadCount: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId) {
        try {
            $query = "UPDATE notifications 
                      SET is_read = 1, updated_at = NOW() 
                      WHERE id = ? AND user_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ii', $notificationId, $userId);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            Log::error('Error in markAsRead: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($userId) {
        try {
            $query = "UPDATE notifications 
                      SET is_read = 1, updated_at = NOW() 
                      WHERE user_id = ? AND is_read = 0";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $userId);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            Log::error('Error in markAllAsRead: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Create a notification
     */
    public function create($data) {
        try {
            $query = "INSERT INTO notifications (user_id, title, message, type, created_at) 
                      VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                'isss',
                $data['userId'],
                $data['title'] ?? '',
                $data['message'],
                $data['type'] ?? 'info'
            );
            
            $stmt->execute();
            return $this->db->insert_id;
            
        } catch (Exception $e) {
            Log::error('Error in create notification: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete a notification (soft delete)
     */
    public function delete($notificationId, $userId) {
        try {
            $query = "UPDATE notifications 
                      SET deleted_at = NOW() 
                      WHERE id = ? AND user_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ii', $notificationId, $userId);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            Log::error('Error in delete notification: ' . $e->getMessage());
            throw $e;
        }
    }
}
