<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Messenger\QaTopicCreateRequest;
use App\Http\Requests\Messenger\QaTopicReplyRequest;
use App\Models\QaTopic;
use App\Models\User;
use Auth;
use Carbon\Carbon;

class MessengerController extends Controller
{
    public function index()
    {
        $topics = QaTopic::where(function ($q) {
            $q->where('creator_id', Auth::id())->orWhere('receiver_id', Auth::id());
        })->orderByDesc('created_at')->get();

        $title   = trans('global.all_messages');
        $unreads = $this->unreadTopics();

        return view('panel.messenger.index', compact('topics', 'title', 'unreads'));
    }

    public function createTopic()
    {
        $users   = User::all()->except(Auth::id());
        $unreads = $this->unreadTopics();

        return view('panel.messenger.create', compact('users', 'unreads'));
    }

    public function storeTopic(QaTopicCreateRequest $request)
    {
        $topic = QaTopic::create([
            'subject'     => $request->input('subject'),
            'creator_id'  => Auth::id(),
            'receiver_id' => $request->input('recipient'),
        ]);

        $topic->messages()->create([
            'sender_id' => Auth::id(),
            'content'   => $request->input('content'),
        ]);

        return redirect()->route('panel.messenger.index');
    }

    public function showMessages(QaTopic $topic)
    {
        $this->checkAccessRights($topic);

        foreach ($topic->messages as $message) {
            if ($message->sender_id !== Auth::id() && $message->read_at === null) {
                $message->read_at = Carbon::now();
                $message->save();
            }
        }

        $unreads = $this->unreadTopics();

        return view('panel.messenger.show', compact('topic', 'unreads'));
    }

    public function destroyTopic(QaTopic $topic)
    {
        $this->checkAccessRights($topic);
        $topic->delete();

        return redirect()->route('panel.messenger.index');
    }

    public function showInbox()
    {
        $title  = trans('global.inbox');
        $topics = QaTopic::where('receiver_id', Auth::id())->orderByDesc('created_at')->get();
        $unreads = $this->unreadTopics();

        return view('panel.messenger.index', compact('topics', 'title', 'unreads'));
    }

    public function showOutbox()
    {
        $title  = trans('global.outbox');
        $topics = QaTopic::where('creator_id', Auth::id())->orderByDesc('created_at')->get();
        $unreads = $this->unreadTopics();

        return view('panel.messenger.index', compact('topics', 'title', 'unreads'));
    }

    public function replyToTopic(QaTopicReplyRequest $request, QaTopic $topic)
    {
        $this->checkAccessRights($topic);

        $topic->messages()->create([
            'sender_id' => Auth::id(),
            'content'   => $request->input('content'),
        ]);

        return redirect()->route('panel.messenger.showMessages', $topic->id);
    }

    public function showReply(QaTopic $topic)
    {
        $this->checkAccessRights($topic);

        $receiverOrCreator = $topic->receiverOrCreator();

        if ($receiverOrCreator === null || $receiverOrCreator->trashed()) {
            abort(404);
        }

        $unreads = $this->unreadTopics();

        return view('panel.messenger.reply', compact('topic', 'unreads'));
    }

    private function unreadTopics(): array
    {
        $topics = QaTopic::where(function ($q) {
            $q->where('creator_id', Auth::id())->orWhere('receiver_id', Auth::id());
        })->with('messages')->orderByDesc('created_at')->get();

        $inboxUnread  = 0;
        $outboxUnread = 0;

        foreach ($topics as $topic) {
            foreach ($topic->messages as $message) {
                if ($message->sender_id !== Auth::id() && $message->read_at === null) {
                    if ($topic->creator_id !== Auth::id()) {
                        $inboxUnread++;
                    } else {
                        $outboxUnread++;
                    }
                }
            }
        }

        return ['inbox' => $inboxUnread, 'outbox' => $outboxUnread];
    }

    private function checkAccessRights(QaTopic $topic)
    {
        if ($topic->creator_id !== Auth::id() && $topic->receiver_id !== Auth::id()) {
            abort(403);
        }
    }
}
