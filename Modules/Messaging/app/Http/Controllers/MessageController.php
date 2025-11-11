<?php

// ============================================
// FILE: Modules/Messaging/app/Http/Controllers/MessageController.php
// ============================================

namespace Modules\Messaging\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Get all messages for current user
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Message::where(function($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->orWhere('receiver_id', $user->id);
        })->with(['sender', 'receiver']);

        // Filter by conversation with specific user
        if ($request->has('user_id')) {
            $query->where(function($q) use ($user, $request) {
                $q->where(function($subQ) use ($user, $request) {
                    $subQ->where('sender_id', $user->id)
                         ->where('receiver_id', $request->user_id);
                })->orWhere(function($subQ) use ($user, $request) {
                    $subQ->where('sender_id', $request->user_id)
                         ->where('receiver_id', $user->id);
                });
            });
        }

        // Filter by estate
        if ($request->has('estate_id')) {
            $query->where('estate_id', $request->estate_id);
        }

        // Filter by read/unread
        if ($request->has('is_read')) {
            $query->where('is_read', $request->is_read);
        }

        $messages = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 50);

        return response()->json([
            'success' => true,
            'data' => $messages
        ], 200);
    }

    /**
     * Send a new message
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'message_type' => 'sometimes|in:direct,announcement,system',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $sender = $request->user();

        // Check if receiver exists
        $receiver = User::find($request->receiver_id);
        if (!$receiver) {
            return response()->json([
                'success' => false,
                'message' => 'Receiver not found'
            ], 404);
        }

        // Cannot send message to yourself
        if ($sender->id == $receiver->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot send message to yourself'
            ], 400);
        }

        $message = Message::create([
            'estate_id' => $sender->estate_id,
            'sender_id' => $sender->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'message_type' => $request->message_type ?? 'direct',
            'is_read' => false,
        ]);

        // TODO: Send push notification to receiver

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => ['message' => $message->load(['sender', 'receiver'])]
        ], 201);
    }

    /**
     * Get all conversations (unique users)
     */
    public function conversations(Request $request)
    {
        $user = $request->user();

        // Get all unique users the current user has chatted with
        $conversations = DB::table('messages')
            ->select(
                DB::raw('CASE
                    WHEN sender_id = ' . $user->id . ' THEN receiver_id
                    ELSE sender_id
                END as user_id'),
                DB::raw('MAX(created_at) as last_message_at'),
                DB::raw('COUNT(CASE WHEN receiver_id = ' . $user->id . ' AND is_read = 0 THEN 1 END) as unread_count')
            )
            ->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->groupBy(DB::raw('CASE
                WHEN sender_id = ' . $user->id . ' THEN receiver_id
                ELSE sender_id
            END'))
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Get user details for each conversation
        $userIds = $conversations->pluck('user_id')->toArray();
        $users = User::whereIn('id', $userIds)
            ->select('id', 'name', 'email', 'phone', 'role', 'uci')
            ->get()
            ->keyBy('id');

        // Merge user details with conversation data
        $conversationsList = $conversations->map(function($conv) use ($users) {
            $userData = $users->get($conv->user_id);
            return [
                'user' => $userData,
                'last_message_at' => $conv->last_message_at,
                'unread_count' => $conv->unread_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => ['conversations' => $conversationsList]
        ], 200);
    }

    /**
     * Get conversation with a specific user
     */
    public function getConversation(Request $request, $userId)
    {
        $currentUser = $request->user();

        // Verify the other user exists
        $otherUser = User::find($userId);
        if (!$otherUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Get all messages between current user and specified user
        $messages = Message::where(function($q) use ($currentUser, $userId) {
            $q->where('sender_id', $currentUser->id)
              ->where('receiver_id', $userId);
        })->orWhere(function($q) use ($currentUser, $userId) {
            $q->where('sender_id', $userId)
              ->where('receiver_id', $currentUser->id);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages from other user as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'other_user' => $otherUser->only(['id', 'name', 'email', 'phone', 'role', 'uci']),
                'messages' => $messages
            ]
        ], 200);
    }

    /**
     * Get unread message count
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();

        $count = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => ['unread_count' => $count]
        ], 200);
    }

    /**
     * Mark message as read
     */
    public function markAsRead($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }

        $message->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read',
            'data' => ['message' => $message]
        ], 200);
    }

    /**
     * Delete message
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $message = Message::find($id);

        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }

        // Only sender can delete their own messages
        if ($message->sender_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this message'
            ], 403);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ], 200);
    }

    /**
     * Broadcast message to all estate users (Estate Admin only)
     */
    public function broadcast(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'target_role' => 'sometimes|in:all,landlords,tenants,security',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $sender = $request->user();

        if (!$sender->isEstateAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only estate admins can broadcast messages'
            ], 403);
        }

        $query = User::where('estate_id', $sender->estate_id)
            ->where('id', '!=', $sender->id)
            ->where('is_active', true);

        // Filter by role if specified
        if ($request->has('target_role') && $request->target_role !== 'all') {
            $role = rtrim($request->target_role, 's'); // Remove trailing 's'
            $query->where('role', $role);
        }

        $recipients = $query->get();

        $messagesSent = 0;
        foreach ($recipients as $recipient) {
            Message::create([
                'estate_id' => $sender->estate_id,
                'sender_id' => $sender->id,
                'receiver_id' => $recipient->id,
                'message' => $request->message,
                'message_type' => 'announcement',
                'is_read' => false,
            ]);
            $messagesSent++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Broadcast sent successfully',
            'data' => ['messages_sent' => $messagesSent]
        ], 200);
    }
}
