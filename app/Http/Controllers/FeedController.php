<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;

class FeedController extends Controller
{
    public function index()
    {
        //return $this->baseIndex();
        return view('feed.index_react');
    }

    public function category($id)
    {
        return $this->baseIndex($id);
    }

    public function history()
    {
        $totalCountReadFeedItems = auth()->user()->feedItems()->read()->count();
        $readFeedItems = auth()->user()->feedItems()->read()->paginate(env('NUMBER_OF_ITEMS_PER_PAGE'));

        return view('feed.history', compact('totalCountReadFeedItems', 'readFeedItems'));
    }

    public function updateFeed()
    {
        $exitCode = Artisan::call('feed:update', [
            'user' => auth()->user()->id
        ]);

        flash()->success(trans('feed.update_feed.success'));

        return redirect()->route('feed.index');
    }

    public function markAllAsRead($categoryId = null)
    {
        $unreadFeedItems = $this->getUnreadFeedItems($categoryId);

        foreach ($unreadFeedItems->get() as $unreadFeedItem) {
            $unreadFeedItem->is_read = true;

            $unreadFeedItem->save();
        }

        flash()->success(trans('feed.mark_all_as_read.success'));

        return redirect()->back();
    }

    public function toggleFeedItemStatus($id)
    {
        $feedItem = auth()->user()->feedItems()->findOrFail($id);

        $feedItem->is_read = !$feedItem->is_read;
        $feedItem->save();

        return response()->json(['isRead' => $feedItem->is_read]);
    }

    public function moreUnread($offset = null, $categoryId = null)
    {
        $unreadFeedItems = $this->getUnreadFeedItems($categoryId);
        $totalCount = $unreadFeedItems->count();

        if ($offset) {
            $unreadFeedItems = $unreadFeedItems->skip($offset);
        }

        $unreadFeedItems = $unreadFeedItems->take(env('NUMBER_OF_ITEMS_PER_PAGE'));

        return response()->json([
            'hasAnotherPage' => $offset + env('NUMBER_OF_ITEMS_PER_PAGE') < $totalCount,
            'nextUrl' => URL::route('feed.more_unread', [$offset + env('NUMBER_OF_ITEMS_PER_PAGE'), $categoryId]),
            'content' => view('feed._more_unread', compact('unreadFeedItems'))->render()
        ]);
    }

    private function baseIndex($categoryId = null)
    {
        $unreadFeedItemsBase = $this->getUnreadFeedItems($categoryId);

        $totalCountUnreadFeedItems = $categoryId == null ? $unreadFeedItemsBase->count() : $this->getUnreadFeedItems()->count();
        $unreadFeedItems = $unreadFeedItemsBase->take(env('NUMBER_OF_ITEMS_PER_PAGE'));

        $categories = auth()->user()->categories();
        $currentCategoryId = $categoryId;

        $hasAnotherPage = env('NUMBER_OF_ITEMS_PER_PAGE') < $unreadFeedItemsBase->count();

        return view('feed.index', compact('totalCountUnreadFeedItems', 'unreadFeedItems', 'categories', 'currentCategoryId', 'hasAnotherPage'));
    }

    private function getUnreadFeedItems($categoryId = null)
    {
        $unreadFeedItemsBase = auth()->user()->feedItems()->unread();

        if ($categoryId != null) {
            $category = auth()->user()->categories()->findOrFail($categoryId);

            $unreadFeedItemsBase = $unreadFeedItemsBase
                                    ->select('feed_items.*', 'feeds.category_id')
                                    ->join('feeds', 'feeds.id', '=', 'feed_items.feed_id')
                                    ->where('category_id', '=', $category->id);
        }

        return $unreadFeedItemsBase;
    }
}